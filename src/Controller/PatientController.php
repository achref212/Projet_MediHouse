<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use App\Form\RegistrationFormType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\RendezVous;
use App\Form\RendezVousType;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Security\Core\Security;
use App\Repository\UserRepository;
use App\Repository\RendezVousRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

#[Route('/patient')]
class PatientController extends AbstractController
{
    #[Route('/', name: 'app_patient')]
    public function index(UserRepository $repo): Response
    {
        $docteurs = $repo->findByRole('ROLE_DOCTOR');

        return $this->render('patient/list_docteur.html.twig', [
            'docteurs' => $docteurs,
        ]);
    }
    #[Route('/', name: 'app_patient_listd')]
    public function list(UserRepository $repo): Response
    {
        $docteurs = $repo->findByPatient();

        return $this->render('patient/list_docteur.html.twig', [
            'docteurs' => $docteurs,
        ]);
    }
    #[Route('/rendez/vous', name: 'app_RendezVous')]
    public function aff(Security $security, RendezVousRepository $RendezVousRepository): Response
    {
        if (!$security->isGranted('ROLE_PATIENT')) {
            return $this->redirectToRoute('app_login');
        }
        $user = $security->getUser();
        return $this->render('rendez_vous/index.html.twig', [
            'RendezVouss' => $RendezVousRepository->findByPatient($user),
        ]);
        if (!$security->isGranted('ROLE_DOCTOR')) {
            return $this->redirectToRoute('app_login');
        }
        $user = $security->getUser();
        return $this->render('rendez_vous/index.html.twig', [
            'RendezVouss' => $RendezVousRepository->findByDocteur($user),
        ]);
    }
    #[Route('/register', name: 'app_patient_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        // 1) build the form
        $patient = new User();
        $form = $this->createForm(RegistrationFormType::class, $patient);

        // 2) handle the submit (will only happen on POST)
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $patient->setRoles(['ROLE_PATIENT']);
            $patient->setPassword(
                $userPasswordHasher->hashPassword(
                    $patient,
                    $form->get('plainPassword')->getData()
                )
            );
            // 4) save the User!

            $entityManager->persist($patient);
            $entityManager->flush();

            return $this->redirectToRoute('app_patient');
        }

        return $this->render(
            'patient/register.html.twig',
            ['registrationForm' => $form->createView()]
        );
    }


    #[Route('/Reserver/{id}', name: 'app_reserver', methods: ['GET', 'POST'])]
    public function reserver(MailerInterface $mailer, $id, UserRepository $repo, Security $security, Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$security->isGranted('ROLE_PATIENT')) {
            return $this->redirectToRoute('app_login');
        }
        $RendezVous = new RendezVous();
        $patient = $security->getToken()->getUser();
        // $patient->addFich($RendezVous->getFiche());
        $Doctor = $repo->find($id);

        $RendezVous->setPatient($patient);
        $RendezVous->setDocteur($repo->find($id));
        $RendezVous->setStatut("Pending");
        $RendezVous->setLocal($Doctor->getadresse());
        $form = $this->createForm(RendezVousType::class, $RendezVous);


        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {



            $RendezVous->setPatient($patient);
            $RendezVous->setDocteur($repo->find($id));
            $RendezVous->setStatut("Pending");
            $RendezVous->setLocal($Doctor->getadresse());
            $entityManager->persist($patient);
            $entityManager->persist($Doctor);


            $entityManager->persist($RendezVous);
            $entityManager->flush();
            $this->sendEmail($mailer, $RendezVous->getPatient()->getEmail());

            return $this->redirectToRoute('app_patient');
        }

        return $this->render(
            'patient/reserver.html.twig',
            [
                'rendezvous' => $RendezVous,
                'form' => $form->createView()
            ]
        );
    }

    #[Route('/rendez/vous', name: 'listRendezVous')]
    public function listR(Security $security, RendezVousRepository $RendezVousRepository): Response
    {
        if (!$security->isGranted('ROLE_PATIENT')) {
            return $this->redirectToRoute('app_login');
        }
        $user = $security->getUser();
        return $this->render('patient/index.html.twig', [
            'RendezVouss' => $RendezVousRepository->findByPatient($user),
        ]);
    }

    public function sendEmail(MailerInterface $mailer, String $emailTo)
    {
        $email = (new TemplatedEmail())
            ->from('mahmoud.gharbi@esprit.tn')
            ->to($emailTo)
            ->subject('Rendez-Vous!')
            ->text('Sending emails is fun again!')
            ->html('<div class=""><div class="aHl"></div><div id=":2b" tabindex="-1"></div><div id=":2u" class="ii gt" jslog="20277; u014N:xr6bB; 1:WyIjdGhyZWFkLWY6MTc1OTY0NDE5ODQ0NTE2MDY2NyIsbnVsbCxudWxsLG51bGwsbnVsbCxudWxsLG51bGwsbnVsbCxudWxsLG51bGwsbnVsbCxudWxsLG51bGwsW11d; 4:WyIjbXNnLWY6MTc1OTY0NDE5ODQ0NTE2MDY2NyIsbnVsbCxbXV0."><div id=":9w" class="a3s aiL msg-2425169272654112974"><div class="adM">             </div><div dir="ltr" style="margin:0px;width:100%;background-color:#f3f2f0;padding:0px;padding-top:8px;font-family:-apple-system,system-ui,BlinkMacSystemFont,Segoe UI,Roboto,Helvetica Neue,Fira Sans,Ubuntu,Oxygen,Oxygen Sans,Cantarell,Droid Sans,Apple Color Emoji,Segoe UI Emoji,Segoe UI Emoji,Segoe UI Symbol,Lucida Grande,Helvetica,Arial,sans-serif"><div class="adM"> </div><div style="height:0px;max-height:0;width:0px;overflow:hidden;opacity:0">Vous avez 1&nbsp;nouvelle invitation</div> <div style="height:0px;max-height:0;width:0px;overflow:hidden;opacity:0"> ͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp; ͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp; ͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp; ͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp; ͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp; ͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp; ͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp; ͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp; ͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp; ͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp; ͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp; ͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp; ͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp; ͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp; ͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp; ͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp; </div> <table role="presentation" valign="top" border="0" cellspacing="0" cellpadding="0" width="512" align="center" class="m_-2425169272654112974mercado-container" style="margin-left:auto;margin-right:auto;margin-top:0px;margin-bottom:0px;width:512px;max-width:512px;background-color:#ffffff;padding:0px"> 
            <tbody>  <tr> <td style="padding-left:24px;padding-right:24px;padding-bottom:24px"> <div> <table role="presentation" valign="top" border="0" cellspacing="0" cellpadding="0" width="100%"> <tbody> <tr> <td style="padding-left:8px;padding-right:8px"> <table role="presentation" valign="top" border="0" cellspacing="0" cellpadding="0" width="100%"> <tbody>
             <tr>
              <td style="padding-bottom:24px;text-align:center;font-size:24px;color:#282828"> Vous avez un rendez-vous </td>
               </tr> <tr>
                <td style="padding-bottom:16px;text-align:center">
                 <a href="" style="color:#0a66c2;display:inline-block;text-decoration:none" target="_blank" data-saferedirecturl="">
                  <img src="https://www.seekpng.com/png/full/298-2986033_calendar-vector-png-1-copie-author.png" alt="Icône d’e-mail ouvert" style="outline:none;text-decoration:none;margin-left:auto;margin-right:auto;display:block;height:64px;width:64px" width="64" height="64" class="CToWUd" data-bit="iit">
                   </a> 
                   </td> 
                   </tr> 
                   <tr> 
                   <td style="text-align:center">
                    <br><p> 
                    </p><table role="presentation" valign="top" border="0" cellspacing="0" cellpadding="0" width="100%"> 
                    <tbody><tr> 
                    <td align="center" style="center:left;font-size:16px;line-height:28px;font-weight:400;font-style:normal;color:#0d0e10;font-family:,Helvetica,Arial,sans-serif!important">
                    Pour consulter les Rendez-Vous clicker sur <strong>Rendez-Vous</strong>
                    </td> 
                    
                    
                    </tr>
                    </tbody><tbody> 
                    <tr> 
                    <td valign="middle" align="middle"> 
                    <a href="https://127.0.0.1:8000/afficheRECl" aria-label="Rendez-Vous" style="color:#0a66c2;display:inline-block;text-decoration:none" target="_blank" data-saferedirecturl=""> 
                    <table role="presentation" valign="top" border="0" cellspacing="0" cellpadding="0" width="auto" style="border-collapse:separate">
                     <tbody> 
                     <tr>
                      <td style="height:min-content;border-radius:24px;padding-top:12px;padding-bottom:12px;padding-left:24px;padding-right:24px;text-align:center;font-size:16px;font-weight:600;text-decoration-line:none;background-color:#0a66c2;color:#ffffff;border-width:1px;border-style:solid;border-color:#0a66c2;line-height:1.25;min-height:auto!important">
                       <a href="https://127.0.0.1:8000/afficheRECl" aria-hidden="true" style="color:#0a66c2;display:inline-block;text-decoration:none" target="_blank" data-saferedirecturl="">
                        <span style="color:#ffffff;text-decoration-line:none">Rendez-Vous</span> 
                        </a> 
                        </td>
                         </tr> 
                         </tbody>
                          </table>
                           </a> </td> </tr> </tbody> </table> </td> </tr> </tbody>
                           </table> </td> </tr> <tr>  </tr>  </tbody> </table> </div> </td> </tr> <tr> <td style="background-color:#f3f2f0;padding:24px"> <table role="presentation" valign="top" border="0" cellspacing="0" cellpadding="0" width="100%"> <tbody> <tr> <td style="padding-bottom:16px;text-align:center"> <h2 style="margin:0;font-weight:500;font-size:20px">Ne manquez aucune nouvelle grâce à l’application Midihouse</h2> </td> </tr> <tr> <td style="text-align:center"> <a href="" style="color:#0a66c2;display:inline-block;text-decoration:none" target="_blank" data-saferedirecturl=""> <img alt="Télécharger sur l’App Store" src="https://ci4.googleusercontent.com/proxy/b2yiCparlDhdmhjje0zuMpyd-xZQqwu0HVF6VC_C6DNDrykN-Eld_Kme0t3qFjtBIbw7yqQVDO2ehlVDMcS1bA8ME6cCQPA2K1oANWk9kdMsWA=s0-d-e1-ft#https://static.licdn.com/aero-v1/sc/h/3hkn71ey3x31zdchx8bgqetc8" style="outline:none;text-decoration:none;height:40px;width:120px;padding-right:8px" width="120" height="40" class="CToWUd" data-bit="iit"> </a><a href="" style="color:#0a66c2;display:inline-block;text-decoration:none" target="_blank" data-saferedirecturl=""> <img alt="Télécharger sur Google Play" src="https://ci3.googleusercontent.com/proxy/IU8nxDwbZ0EJqqEjcgK_KsgMVf1vPLYc4xqxYjErlCG2xJTg3jwqYZTFGrJ1uLuaSu-KWS7dVyj2DhYbxRqRCJSVbhLOJLix-_IQ5X-Q8_PWjg=s0-d-e1-ft#https://static.licdn.com/aero-v1/sc/h/cq08p5bwlxk1zent36f22et3h" style="outline:none;text-decoration:none;height:40px;width:134px" width="134" height="40" class="CToWUd" data-bit="iit"> </a> </td> </tr> <tr> <td style="padding-top:16px;padding-bottom:16px"><hr style="height:1px;border-style:none;background-color:#e0dfdd"></td> </tr> </tbody> </table> <table role="presentation" valign="top" border="0" cellspacing="0" cellpadding="0" width="100%" style="font-size:12px"> <tbody> <tr> <td style="margin:0px;padding-bottom:8px"> Cet e-mail était traité par l’admin </td> </tr>     <tr>  <td style="margin:0px;padding-bottom:8px"> © 2023 MidiHouse  </td></tr> <tr>  <td>Contact Us:
 </td></tr><tr> 
    <td>esprit ghazala tunisie
 </td></tr><tr>  <td>+216 55 676 837
 </td></tr><tr>  <td>contact@medihouse.com </td></tr></tbody> </table> </td> </tr> </tbody> </table> <img alt="" role="presentation" src="https://ci6.googleusercontent.com/proxy/NH6-G-G5GNQMR1ptYqdxrFYIrQNHUwFKB7Hba_AmecBDEYg2fr4lGueSmvhBkSlzUep_2eHJQXRzwNK1zhR6xV4YxB3gOQu0oL5zKuqdmYRRyRKQN0JmsVRWHnL1i80MjhbmAcszEromW4J3G-_HXPSqCrvpXk647nR4kBKeciMAyrxCYo1-b4MmtjrIU-Fq6g=s0-d-e1-ft#https://www.linkedin.com/emimp/ip_WkhscVluUnRMV3hsZURWeWEzUm1MV054OlpXMWhhV3hmYm05MGFXWnBZMkYwYVc5dVgyUnBaMlZ6ZEY4d01RPT06.gif" style="outline:none;text-decoration:none;width:1px;height:1px" width="1" height="1" class="CToWUd" data-bit="iit"><div class="yj6qo"></div><div class="adL"> </div></div><div class="adL"> </div></div></div><div id=":2l" class="ii gt" style="display:none"><div id=":2k" class="a3s aiL "></div></div><div class="hi"></div></div>');

        $mailer->send($email);

        return new Response("Success");
    }
}
