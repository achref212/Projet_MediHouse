<?php

namespace App\Controller;

use App\Form\ForgotPasswordType;
use App\Form\ForgotPasswordType1;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class SecurityController extends AbstractController
{
    #[Route('/security', name: 'app_security')]
    public function index(): Response
    {
        return $this->render('security/index.html.twig', [
            'controller_name' => 'SecurityController',
        ]);
    }

    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/loginAdmin', name: 'app_login_Admin')]
    public function loginA(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/loginA.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }


    #[Route(path: '/forgot', name: 'forgot')]

    public function forgotPassword(Request $request, UserRepository $userRepository, MailerInterface $mailer, TokenGeneratorInterface  $tokenGenerator)
    {


        $form = $this->createForm(ForgotPasswordType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $donnees = $form->getData(); //


            $user = $userRepository->findOneBy(['email' => $donnees]);
            if (!$user) {
                $this->addFlash('danger', 'cette adresse n\'existe pas');
                return $this->redirectToRoute("forgot");
            }
            $token = $tokenGenerator->generateToken();

            try {
                $user->setResetToken($token);
                $entityManger = $this->getDoctrine()->getManager();
                $entityManger->persist($user);
                $entityManger->flush();
            } catch (\Exception $exception) {
                $this->addFlash('warning', 'une erreur est survenue :' . $exception->getMessage());
                return $this->redirectToRoute("app_login");
            }

            $url = $this->generateUrl('app_reset_password', array('token' => $token), UrlGeneratorInterface::ABSOLUTE_URL);

            //BUNDLE MAILER
            $email = (new TemplatedEmail())
                ->from('achref.chaabani@esprit.tn')
                ->to('achref.chaabani@esprit.tn')

                ->subject('Time for Symfony Mailer!')
                ->text('Sending emails is fun again!')
                ->html('<p>See Twig integration for better HTML integration!</p>');

            $mailer->send($email);
            //    return $this->redirectToRoute("app_login");



        }

        return $this->render("security/forgotPassword.html.twig", ['form' => $form->createView()]);
    }


    /**
     * @Route("/resetpassword/{token}", name="app_reset_password")
     */
    public function resetpassword(Request $request, string $token, UserPasswordEncoderInterface  $passwordEncoder)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['reset_token' => $token]);

        if ($user == null) {
            $this->addFlash('danger', 'TOKEN INCONNU');
            return $this->redirectToRoute("app_login");
        }

        if ($request->isMethod('POST')) {
            $user->setResetToken(null);

            $user->setPassword($passwordEncoder->encodePassword($user, $request->request->get('password')));
            $entityManger = $this->getDoctrine()->getManager();
            $entityManger->persist($user);
            $entityManger->flush();

            $this->addFlash('message', 'Mot de passe mis à jour :');
            return $this->redirectToRoute("app_login");
        } else {
            return $this->render("security/resetPassword.html.twig", ['token' => $token]);
        }
    }
}
