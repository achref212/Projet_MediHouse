<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Entity\User;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Validator\Constraints\Json;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use Normalizer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use symfony\Component\Serializer\Annotation\Groups;

class UtilisateurApiController extends AbstractController
{


    #[Route('/registerPP', name: 'app_registerPP')]
    public function registerPJ(Request $request, NormalizerInterface $serial, UserPasswordHasherInterface $userPasswordHasher)
    {
        $email = $request->query->get("email");
        $nom = $request->query->get("nom");
        $prenom = $request->query->get("prenom");
        $telephone = $request->query->get("telephone");
        $genre = $request->query->get("genre");
        $roles = $request->query->get("roles");
        $password = $request->query->get("password");
        $adresse = $request->query->get("adresse");
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return new Response("email invalid.");
        }

        $user = new User();
        $user->setRoles(['ROLE_PATIENT']);
        $user->setEmail($email);
        $user->setnom($nom);
        $user->setprenom($prenom);
        $user->setTelephone($telephone);
        $user->setGenre($genre);
        $user->setadresse($adresse);
        $user->setRoles(array($roles));
        $user->setPassword(
            $userPasswordHasher->hashPassword(
                $user,
                $password
            )
        );
        $user->setActivate(true);
        try {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return new JsonResponse("Account is created", 200);
        } catch (\Exception $ex) {
            return new Response("exception" . $ex->getMessage());
        }
    }
    #[Route('/loginJSON', name: 'app_loginJson')]
    public function login(AuthenticationUtils $authenticationUtils, Request $request)
    {
        $email = $request->query->get("email");
        $password = $request->query->get("password");
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->findOneBy(['email' => $email]);
        if ($user) {
            if (password_verify($password, $user->getPassword())) {
                $serializer = new Serializer([new ObjectNormalizer()]);
                $formatted = $serializer->normalize($user);
                return new JsonResponse($formatted);
            } else {
                return new Response("password not found");
            }
        } else {
            return new Response("user not found");
        }
    }

    #[Route('/Alluser', name: 'app_All_User_Json')]
    public function Users(UserRepository $repo, NormalizerInterface $normalizer)
    {
        $user = $repo->findAll();
        $userNormalises = $normalizer->normalize($user, 'json', ['groups' => "user"]);
        $json = json_encode($userNormalises);
        return new Response($json);
    }



    #[Route('/editProfileJSON', name: 'app_edit_profile_Json')]
    public function editProfile(AuthenticationUtils $authenticationUtils, Request $request, UserPasswordHasherInterface $userPasswordHasher)
    {
        $id = $request->get("id");
        $email = $request->query->get("email");
        $nom = $request->query->get("nom");
        $prenom = $request->query->get("prenom");
        $telephone = $request->query->get("telephone");
        $genre = $request->query->get("genre");
        $adresse = $request->query->get("adresse");
        $password = $request->query->get("password");
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($id);
        if ($request->files->get("profilepicture") != null) {
            $file = $request->files->get("profilepicture");
            $filename = $file->getProfilepicture();
            $file->move($filename);
            $user->setProfilepicture($filename);
        }
        $user->setEmail($email);
        $user->setPassword(
            $userPasswordHasher->hashPassword(
                $user,
                $password
            )
        );
        $user->setActivate(true);
        $user->setnom($nom);
        $user->setprenom($prenom);
        $user->setTelephone($telephone);
        $user->setGenre($genre);
        $user->setadresse($adresse);
        try {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return new JsonResponse("success", 200);
        } catch (\Exception $ex) {
            return new Response("failed" . $ex->getMessage());
        }
    }
}
