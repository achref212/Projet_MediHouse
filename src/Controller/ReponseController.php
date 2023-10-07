<?php

namespace App\Controller;

use App\Entity\Reponse;
use App\Entity\Question;
use App\Form\ReponseType;
use App\Form\QuestionType;
use App\Repository\ReponseRepository;
use App\Repository\QuestionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/reponse')]
class ReponseController extends AbstractController
{
    #[Route('/', name: 'app_reponse_index', methods: ['GET'])]
    public function index(ReponseRepository $reponseRepository, QuestionRepository $questionRepository): Response
    {
        return $this->render('reponse/index.html.twig', [
            'reponses' => $reponseRepository->findAll(), 'questions' => $questionRepository->findAll(),
        ]);
    }

    #[Route('/{id}', name: 'app_reponse_delete1', methods: ['POST'])]
    public function dele(Request $request, Reponse $reponse, ReponseRepository $reponseRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $reponse->getId(), $request->request->get('_token'))) {
            $reponseRepository->remove($reponse, true);
        }

        return $this->redirectToRoute('app_reponse_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/new/re', name: 'app_reponse_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ReponseRepository $reponseRepository, Security $security): Response
    {
        $reponse = new Reponse();
        $question_id = $request->query->get('question_id');
        $reponse->setIdQuestion($question_id);
        $user = $security->getToken()->getUser();
        $reponse->setuser($user);
        $form = $this->createForm(ReponseType::class, $reponse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reponseRepository->save($reponse, true);

            return $this->redirectToRoute('app_question_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reponse/new.html.twig', [
            'reponse' => $reponse,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reponse_show', methods: ['GET'])]
    public function show(Reponse $reponse): Response
    {
        return $this->render('question/show.html.twig', [
            'reponse' => $reponse,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_reponse_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reponse $reponse, ReponseRepository $reponseRepository): Response
    {
        $form = $this->createForm(ReponseType::class, $reponse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reponseRepository->save($reponse, true);

            return $this->redirectToRoute('app_question_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reponse/edit.html.twig', [
            'reponse' => $reponse,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reponse_delete', methods: ['POST'])]
    public function delete(Request $request, Reponse $reponse, ReponseRepository $reponseRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $reponse->getId(), $request->request->get('_token'))) {
            $reponseRepository->remove($reponse, true);
        }

        return $this->redirectToRoute('app_question_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/reponse/{id}/like', name: 'reponse_like')]
    #[Route('/reponse/{id}/dislike', name: 'reponse_dislike')]
    public function likeOrDislike(reponse $Question, Request $request, SessionInterface $session): Response
    {
        $liked = $session->get('liked_reponse', []);
        $disliked = $session->get('disliked_reponse', []);

        if ($request->get('_route') === 'reponse_like' && !in_array($Question->getId(), $liked) && !in_array($Question->getId(), $disliked)) {
            $Question->setLikes($Question->getLikes() + 1);
            $liked[] = $Question->getId();
            $session->set('liked_reponse', $liked);
        } elseif ($request->get('_route') === 'reponse_like' && in_array($Question->getId(), $liked)) {
            $Question->setLikes($Question->getLikes() - 1);
            $liked = array_diff($liked, [$Question->getId()]);
            $session->set('liked_reponse', $liked);
        } elseif ($request->get('_route') === 'reponse_dislike' && !in_array($Question->getId(), $liked) && !in_array($Question->getId(), $disliked)) {
            $Question->setDislikes($Question->getDislikes() + 1);
            $disliked[] = $Question->getId();
            $session->set('disliked_reponse', $disliked);
        } elseif ($request->get('_route') === 'reponse_dislike' && in_array($Question->getId(), $disliked)) {
            $Question->setDislikes($Question->getDislikes() - 1);
            $disliked = array_diff($disliked, [$Question->getId()]);
            $session->set('disliked_reponse', $disliked);
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($Question);
        $entityManager->flush();
        return $this->redirectToRoute('app_question_index', ['id' => $Question->getId()]);
    }
}
