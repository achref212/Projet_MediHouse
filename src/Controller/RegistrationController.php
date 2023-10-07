<?php

namespace App\Controller;

use App\Repository\ReponseRepository;
use App\Repository\QuestionRepository;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Form\UserType;
use App\Form\UserType1;
use App\Entity\Fiche;
use App\Entity\RendezVous;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use App\Repository\RendezVousRepository;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Repository\ReclamationRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class RegistrationController extends AbstractController
{
    //Profile
    #[Route('/profile', name: 'user_profile')]
    public function userProfile(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();

        return $this->render('profile.html.twig', [
            'user' => $user,
        ]);
    }
    #[Route('/admin_profile', name: 'admin_profile')]
    public function adminProfile(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();

        return $this->render('profileA.html.twig', [
            'user' => $user,

        ]);
    }
    //Edit Profile
    #[Route('/profile/{id}', name: 'user_profile_edit', methods: ['GET', 'POST'])]
    public function editUserProfile(Request $request, user $user, UserRepository $UserRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $form = $this->createForm(UserType1::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $UserRepository->save($user, true);

            return $this->redirectToRoute('user_profile', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('profile_edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/admin_profile/{id}', name: 'admin_profile_edit', methods: ['GET', 'POST'])]
    public function editAdminProfile(Request $request, user $user, UserRepository $UserRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $form = $this->createForm(UserType1::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $UserRepository->save($user, true);

            return $this->redirectToRoute('admin_profile', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin_edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }


    //Lists
    #[Route('/doctors', name: 'user_doctors')]
    public function listDoctor(UserRepository $userRepository): Response
    {

        // Vérifie que l'utilisateur connecté a le rôle ROLE_ADMIN
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Récupère la liste des utilisateurs ayant le rôle ROLE_DOCTOR
        $doctors = $userRepository->findByRole('ROLE_DOCTOR');

        // Affiche la vue avec la liste des utilisateurs
        return $this->render('user/list_doctor.html.twig', [
            'doctors' => $doctors,
        ]);
    }

    #[Route('/patients', name: 'user_patients')]
    public function patientList(UserRepository $userRepository): Response
    {

        // Vérifie que l'utilisateur connecté a le rôle ROLE_ADMIN
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Récupère la liste des utilisateurs ayant le rôle ROLE_PATIENT
        $patients = $userRepository->findByRole('ROLE_PATIENT');

        // Affiche la vue avec la liste des utilisateurs
        return $this->render('user/list_patient.html.twig', [
            'patients' => $patients,
        ]);
    }

    #[Route('/paras', name: 'user_paras')]
    public function listPara(Security $security, UserRepository $userRepository): Response
    {

        // Vérifie que l'utilisateur connecté a le rôle ROLE_ADMIN
        if (!$security->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_login');
        }

        // Récupère la liste des utilisateurs ayant le rôle ROLE_PARA
        $paras = $userRepository->findByRole('ROLE_PARA');

        // Affiche la vue avec la liste des utilisateurs
        return $this->render('user/list_para.html.twig', [
            'paras' => $paras,
        ]);
    }

    #[Route('/Rendez_Vous', name: 'app_listRDV')]
    public function listRDV1(Security $security, RendezVousRepository $RendezVousRepository): Response
    { {
            if (!$security->isGranted('ROLE_ADMIN')) {
                return $this->redirectToRoute('app_login');
            }
            $user = $security->getUser();
            return $this->render('user/list_RDV.html.twig', [
                'RendezVouss' => $RendezVousRepository->findAll(),
            ]);
        }
    }

    //INDEX

    #[Route('/home', name: 'app_patient_index', methods: ['GET'])]
    public function home(UserRepository $patientRepository): Response
    {

        $this->denyAccessUnlessGranted('ROLE_USER');

        return $this->render('home/index.html.twig', [
            'patients' => $patientRepository->findAll(),
        ]);
    }
    #[Route('/admin', name: 'app_Admin_index', methods: ['GET'])]
    public function Admin(QuestionRepository $questionRepository, ReponseRepository $reponseRepository, RendezVousRepository $RendezVousRepository, UserRepository $UserRepository, ReclamationRepository $repo): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $RoleCount = [];

        //$users = $UserRepository->findAll();
        $Admins = $UserRepository->findByRole('ROLE_Admin');
        $Patients = $UserRepository->findByRole('ROLE_PATIENT');
        $Docteurs = $UserRepository->findByRole('ROLE_DOCTOR');
        $para = $UserRepository->findByRole('ROLE_PARA');


        // On "démonte" les données pour les séparer tel qu'attendu par ChartJS

        $RoleCount = [count($Docteurs), count($Patients), count($Admins), count($para)];


        $RendezVous = $RendezVousRepository->countByDate();

        $dates = [];
        $RendezVousCount = [];

        // On "démonte" les données pour les séparer tel qu'attendu par ChartJS
        foreach ($RendezVous as $RendezVou) {
            $dates[] = $RendezVou['date'];
            $RendezVousCount[] = $RendezVou['count'];
        }


        $SujetCount = [];

        //$users = $UserRepository->findAll();
        $Medecin = $repo->findBySujet("medcin");
        $Patient = $repo->findBySujet("patient");
        $Autre = $repo->findBySujet("Autres");



        // On "démonte" les données pour les séparer tel qu'attendu par ChartJS

        $SujetCount = [count($Medecin), count($Patient), count($Autre)];

        $numQuestions = $questionRepository->createQueryBuilder('q')->select('COUNT(q.id)')->getQuery()->getSingleScalarResult();
        $numResponses = $reponseRepository->createQueryBuilder('r')->select('COUNT(r.id)')->getQuery()->getSingleScalarResult();

        // Generate the data for the chart
        $chartData = [
            'labels' => ['Questions', 'Responses'],
            'datasets' => [
                [
                    'label' => 'Number of items',
                    'data' => [$numQuestions, $numResponses],
                    'backgroundColor' => [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                    ],
                    'borderColor' => [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                    ],
                    'borderWidth' => 1
                ]
            ]
        ];

        return $this->render('Admin.html.twig', [
            'RoleCount' => json_encode($RoleCount),
            'dates' => json_encode($dates),
            'RendezVousCount' => json_encode($RendezVousCount),
            'SujetCount' => json_encode($SujetCount),
            'chartData' => json_encode($chartData)
        ]);
    }

    #[Route('/404', name: 'app_Ban', methods: ['GET'])]
    public function Bane(): Response
    {

        return $this->render('404.html.twig');
    }
    //Register

    #[Route('/registration', name: 'app_registration')]
    public function index(): Response
    {
        return $this->render('registration/register.html.twig', [
            'controller_name' => 'RegistrationController',
        ]);
    }

    #[Route('/registerAdmin', name: 'app_registerA')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setRoles(['ROLE_ADMIN']);
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/registerA.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/registerP', name: 'app_registerP')]
    public function registerP(SluggerInterface $slugger, MailerInterface $mailer, Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setRoles(['ROLE_PATIENT']);
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            $email = (new TemplatedEmail())
                ->from('achref.chaabani@esprit.tn')
                ->to($user->getEmail())

                ->subject('Time for Symfony Mailer!')
                ->text('Sending emails is fun again!')
                ->html('<p>See Twig integration for better HTML integration!</p>');

            $mailer->send($email);
            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/registerP.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/registerD', name: 'app_registerD')]
    public function registerD(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setRoles(['ROLE_DOCTOR']);
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/registerD.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/registerPara', name: 'app_registerPara')]
    public function registerPara(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $user->setRoles(['ROLE_PARA']);
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/registerPara.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('ban/{id}', name: 'ban')]
    public function ban($id): Response
    {
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);
        $user->setActivate(false);
        $this->getDoctrine()->getManager()->flush();
        return $this->redirectToRoute('app_Admin_index');
    }

    #[Route('activate/{id}', name: 'activate')]
    public function activate($id): Response
    {
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);
        $user->setActivate(true);
        $this->getDoctrine()->getManager()->flush();
        return $this->redirectToRoute('app_Admin_index');
    }
}
