<?php

namespace App\Controller;

use App\Entity\Reponse1;
use App\Form\Reponse1Type;
use App\Repository\Reponse1Repository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\ReclamationType;
use App\Repository\ReclamationRepository;
use App\Entity\Reclamation;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;


#[Route('/Reponse1')]
class Reponse1Controller extends AbstractController
{
    #[Route('/', name: 'app_Reponse1_index', methods: ['GET'])]
    public function index(ReclamationRepository $repository): Response
    {
        return $this->render('reclamation/listR.html.twig', [
            'reclamations' => $repository->findAll(),
        ]);
    }

    #[Route('/new/{id}', name: 'app_Reponse1_new', methods: ['POST', 'GET'])]
    public function new(MailerInterface $mailer, Request $request, $id, ReclamationRepository $RecRepository, Reponse1Repository $Reponse1Repository): Response
    {
        $Reponse1 = new Reponse1();



        $form = $this->createForm(Reponse1Type::class, $Reponse1);
        $form->handleRequest($request);
        $Reclamation = $RecRepository->find($id);
        if ($form->isSubmitted()) {
            // $Reponse1->setReclamation($RecRepository->get_reclamation($id));
            $this->sendEmail($mailer, $Reclamation->getEmail());
            $Reponse1Repository->save($Reponse1, true);

            return $this->redirectToRoute('app_Reponse1_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reponse1/new.html.twig', [
            'Reponse1' => $Reponse1,
            'formClass' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_Reponse1_show', methods: ['GET'])]
    public function show(Reponse1 $Reponse1): Response
    {
        return $this->render('reponse1/show.html.twig', [
            'Reponse1' => $Reponse1,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_Reponse1_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reponse1 $Reponse1, Reponse1Repository $Reponse1Repository): Response
    {
        $form = $this->createForm(Reponse1Type::class, $Reponse1);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $Reponse1Repository->save($Reponse1, true);

            return $this->redirectToRoute('app_Reponse1_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('Reponse1/edit.html.twig', [
            'Reponse1' => $Reponse1,
            'formClass' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_Reponse1_delete', methods: ['POST'])]
    public function delete(Request $request, Reponse1 $Reponse1, Reponse1Repository $Reponse1Repository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $Reponse1->getId(), $request->request->get('_token'))) {
            $Reponse1Repository->remove($Reponse1, true);
        }

        return $this->redirectToRoute('app_Reponse1_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route("/email", name: "email")]

    public function sendEmail(MailerInterface $mailer, string $emailto)
    {
        $email = (new TemplatedEmail())
            ->from('hdhia46@gmail.com')
            ->to($emailto)
            ->subject('Réclamation traité')
            ->html('<div class=""><div class="aHl"></div><div id=":2b" tabindex="-1"></div><div id=":2u" class="ii gt" jslog="20277; u014N:xr6bB; 1:WyIjdGhyZWFkLWY6MTc1OTY0NDE5ODQ0NTE2MDY2NyIsbnVsbCxudWxsLG51bGwsbnVsbCxudWxsLG51bGwsbnVsbCxudWxsLG51bGwsbnVsbCxudWxsLG51bGwsW11d; 4:WyIjbXNnLWY6MTc1OTY0NDE5ODQ0NTE2MDY2NyIsbnVsbCxbXV0."><div id=":9w" class="a3s aiL msg-2425169272654112974"><div class="adM">             </div><div dir="ltr" style="margin:0px;width:100%;background-color:#f3f2f0;padding:0px;padding-top:8px;font-family:-apple-system,system-ui,BlinkMacSystemFont,Segoe UI,Roboto,Helvetica Neue,Fira Sans,Ubuntu,Oxygen,Oxygen Sans,Cantarell,Droid Sans,Apple Color Emoji,Segoe UI Emoji,Segoe UI Emoji,Segoe UI Symbol,Lucida Grande,Helvetica,Arial,sans-serif"><div class="adM"> </div><div style="height:0px;max-height:0;width:0px;overflow:hidden;opacity:0">Vous avez 1&nbsp;nouvelle invitation</div> <div style="height:0px;max-height:0;width:0px;overflow:hidden;opacity:0"> ͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp; ͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp; ͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp; ͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp; ͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp; ͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp; ͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp; ͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp; ͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp; ͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp; ͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp; ͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp; ͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp; ͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp; ͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp; ͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp;͏&nbsp; </div> <table role="presentation" valign="top" border="0" cellspacing="0" cellpadding="0" width="512" align="center" class="m_-2425169272654112974mercado-container" style="margin-left:auto;margin-right:auto;margin-top:0px;margin-bottom:0px;width:512px;max-width:512px;background-color:#ffffff;padding:0px"> <tbody>  <tr> <td style="padding-left:24px;padding-right:24px;padding-bottom:24px"> <div> <table role="presentation" valign="top" border="0" cellspacing="0" cellpadding="0" width="100%"> <tbody> <tr> <td style="padding-left:8px;padding-right:8px"> <table role="presentation" valign="top" border="0" cellspacing="0" cellpadding="0" width="100%"> <tbody>
             <tr>
              <td style="padding-bottom:24px;text-align:center;font-size:24px;color:#282828"> Vous avez une Reponse1 </td>
               </tr> <tr>
                <td style="padding-bottom:16px;text-align:center">
                 <a href="" style="color:#0a66c2;display:inline-block;text-decoration:none" target="_blank" data-saferedirecturl="">
                  <img src="https://ci3.googleusercontent.com/proxy/7xElyMl9VZtHhSkrLDJTLYkoQQVNBma884xiPrq6NiScDc3SY_hq4QWwsOYpS0fMMdfIgC-FvWxJOz1veH6_ih4NFKUF8-Z8eUyeC7M4eFUONA=s0-d-e1-ft#https://static.licdn.com/aero-v1/sc/h/1uvhgehc32ggjukldm6o9dqfs" alt="Icône d’e-mail ouvert" style="outline:none;text-decoration:none;margin-left:auto;margin-right:auto;display:block;height:64px;width:64px" width="64" height="64" class="CToWUd" data-bit="iit">
                   </a> 
                   </td> 
                   </tr> 
                   <tr> 
                   <td style="text-align:center">
                    <br><p> 
                    </p><table role="presentation" valign="top" border="0" cellspacing="0" cellpadding="0" width="100%"> 
                    <tbody><tr> 
                    <td align="center" style="center:left;font-size:16px;line-height:28px;font-weight:400;font-style:normal;color:#0d0e10;font-family:,Helvetica,Arial,sans-serif!important">
                    Pour consulter la Reponse1 clicker sur <strong>Voir les reclamations</strong>
                    </td> 
                    
                    
                    </tr>
                    </tbody><tbody> 
                    <tr> 
                    <td valign="middle" align="middle"> 
                    <a href="https://127.0.0.1:8000/afficheRECl" aria-label="Voir les réclamations" style="color:#0a66c2;display:inline-block;text-decoration:none" target="_blank" data-saferedirecturl=""> 
                    <table role="presentation" valign="top" border="0" cellspacing="0" cellpadding="0" width="auto" style="border-collapse:separate">
                     <tbody> 
                     <tr>
                      <td style="height:min-content;border-radius:24px;padding-top:12px;padding-bottom:12px;padding-left:24px;padding-right:24px;text-align:center;font-size:16px;font-weight:600;text-decoration-line:none;background-color:#0a66c2;color:#ffffff;border-width:1px;border-style:solid;border-color:#0a66c2;line-height:1.25;min-height:auto!important">
                       <a href="https://127.0.0.1:8000/afficheRECl" aria-hidden="true" style="color:#0a66c2;display:inline-block;text-decoration:none" target="_blank" data-saferedirecturl="">
                        <span style="color:#ffffff;text-decoration-line:none"> Voir les réclamations </span> 
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
