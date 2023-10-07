<?php

namespace App\Controller;

use App\Entity\Reponse;
use App\Entity\Question;
use App\Form\QuestionType;
use App\Repository\ReponseRepository;
use App\Repository\QuestionRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Normalizer\NormalizableInterface;

#[Route('/question')]
class QuestionController extends AbstractController

{


    #[Route('/', name: 'app_question_index', methods: ['GET'])]
    public function index(QuestionRepository $questionRepository, ReponseRepository $reponseRepository, Request $request, PaginatorInterface $paginator): Response
    {

        $ques = $questionRepository->findBy([], ['date_pub' => 'DESC']);
        $ques = $paginator->paginate($ques, $request->query->getInt('page', 1), 2);

        return $this->render('question/index.html.twig', [
            'questions' => $ques,
            'reponses' => $reponseRepository->findAll(),
        ]);
    }


    #[Route("/json/all", name: "list")]
    //* Dans cette fonction, nous utilisons les services NormlizeInterface et StudentRepository, 
    //* avec la méthode d'injection de dépendances.
    public function getStudents(QuestionRepository $repo, SerializerInterface $serializer)
    {
        $students = $repo->findAll();
        //* Nous utilisons la fonction normalize qui transforme le tableau d'objets 
        //* students en  tableau associatif simple.
        // $studentsNormalises = $normalizer->normalize($students, 'json', ['groups' => "students"]);

        // //* Nous utilisons la fonction json_encode pour transformer un tableau associatif en format JSON
        // $json = json_encode($studentsNormalises);

        $json = $serializer->serialize($students, 'json');

        //* Nous renvoyons une réponse Http qui prend en paramètre un tableau en format JSON
        return new Response($json);
    }
    #[Route("/json/new", name: "addStudentJSON")]
    public function addStudentJSON(Request $req,   NormalizerInterface $Normalizer)
    {

        $em = $this->getDoctrine()->getManager();
        $student = new Question();
        $student->setQuestion($req->get('question'));
        $em->persist($student);
        $em->flush();

        $jsonContent = $Normalizer->normalize($student, 'json');
        return new Response("question ajouté avec succée");
    }


    #[Route("/json/find/{id}", name: "student")]
    //* Dans cette fonction, nous utilisons les services NormlizeInterface et StudentRepository, 
    //* avec la méthode d'injection de dépendances.
    public function StudentId($id, NormalizerInterface $normalizer, QuestionRepository $repo)
    {
        $student = $repo->find($id);
        //* Nous utilisons la fonction normalize qui transforme le tableau d'objets 
        //* students en  tableau associatif simple.
        // $studentsNormalises = $normalizer->normalize($students, 'json', ['groups' => "students"]);

        // //* Nous utilisons la fonction json_encode pour transformer un tableau associatif en format JSON
        // $json = json_encode($studentsNormalises);

        $studentNormalises = $normalizer->normalize($student, 'json');
        return new Response(json_encode($studentNormalises));
    }


    #[Route("/json/update/{id}", name: "updateStudentJSON")]
    public function updateStudentJSON(Request $req, $id, NormalizerInterface $Normalizer)
    {

        $em = $this->getDoctrine()->getManager();
        $student = $em->getRepository(Question::class)->find($id);
        $student->setquestion($req->get('question'));

        $em->flush();

        $jsonContent = $Normalizer->normalize($student, 'json');
        return new Response("question updated successfully ");
    }

    #[Route("/json/delete/{id}", name: "deleteStudentJSON")]
    public function deletequestionJSON(Request $req, $id, NormalizerInterface $Normalizer)
    {

        $em = $this->getDoctrine()->getManager();
        $student = $em->getRepository(Question::class)->find($id);
        $em->remove($student);
        $em->flush();
        $jsonContent = $Normalizer->normalize($student, 'json');
        return new Response("question deleted successfully ");
    }



    #[Route('/new', name: 'app_question_new', methods: ['GET', 'POST'])]
    public function new(Request $request, QuestionRepository $questionRepository): Response
    {
        $question = new Question();
        $form = $this->createForm(QuestionType::class, $question);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $questionRepository->save($question, true);

            $this->addFlash('success', 'Publication Ajoutée!');

            return $this->redirectToRoute('app_question_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('question/new.html.twig', [
            'question' => $question,
            'form' => $form, 'message' => $this
        ]);
    }



    #[Route('/afficher/{id}', name: 'app_question_show', methods: ['GET'])]
    public function show(Question $question, ReponseRepository $reponseRepository): Response
    {
        return $this->render('question/show.html.twig', [
            'question' => $question,
            'reponses' => $reponseRepository->findAll(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_question_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Question $question, QuestionRepository $questionRepository): Response
    {
        $form = $this->createForm(QuestionType::class, $question);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $questionRepository->save($question, true);

            return $this->redirectToRoute('app_question_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('question/edit.html.twig', [
            'question' => $question,
            'form' => $form,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_question_delete', methods: ['POST'])]
    public function delete(Request $request, Question $question, QuestionRepository $questionRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $question->getId(), $request->request->get('_token'))) {
            $questionRepository->remove($question, true);
        }

        return $this->redirectToRoute('app_question_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/admin/{id}', name: 'delete', methods: ['POST'])]
    public function delete1(Request $request, Question $question, QuestionRepository $questionRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $question->getId(), $request->request->get('_token'))) {
            $questionRepository->remove($question, true);
        }

        return $this->redirectToRoute('app_reponse_index', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/Question/{id}/like', name: 'Question_like')]
    #[Route('/Question/{id}/dislike', name: 'Question_dislike')]
    public function likeOrDislike(Question $Question, Request $request,SessionInterface $session): Response
    {
        $liked = $session->get('liked_Questions', []);
        $disliked = $session->get('disliked_Questions', []);

        if ($request->get('_route') === 'Question_like' && !in_array($Question->getId(), $liked) && !in_array($Question->getId(), $disliked)) {
            $Question->setLikes($Question->getLikes() + 1);
            $liked[] = $Question->getId();
            $session->set('liked_Questions', $liked);
        } elseif ($request->get('_route') === 'Question_like' && in_array($Question->getId(), $liked)) {
            $Question->setLikes($Question->getLikes() - 1);
            $liked = array_diff($liked, [$Question->getId()]);
            $session->set('liked_Questions', $liked);
        } elseif ($request->get('_route') === 'Question_dislike' && !in_array($Question->getId(), $liked) && !in_array($Question->getId(), $disliked)) {
            $Question->setDislikes($Question->getDislikes() + 1);
            $disliked[] = $Question->getId();
            $session->set('disliked_Questions', $disliked);
        } elseif ($request->get('_route') === 'Question_dislike' && in_array($Question->getId(), $disliked)) {
            $Question->setDislikes($Question->getDislikes() - 1);
            $disliked = array_diff($disliked, [$Question->getId()]);
            $session->set('disliked_Questions', $disliked);
        } 

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($Question);
        $entityManager->flush();
        return $this->redirectToRoute('app_question_index', ['id' => $Question->getId()]);
    }

    #[Route('/share/facebook/{id}', name: 'ssd')]
    public function share($id, Request $request, QuestionRepository $repo): Response
    {
        $publication = $repo->find($id);

        $hashtag = "#" . str_replace(' ', '', $publication->getQuestion());
        $homepageUrl = "http://127.0.0.1:8000/";
        $shareUrl = "https://www.facebook.com/dialog/share?app_id=160291406462337&display=popup&href=" . urlencode($homepageUrl);
        $shareUrl .= "&hashtag=" . urlencode($hashtag);

        return $this->redirect($shareUrl);
    }

    #[Route('/share/twitter/{id}', name: 'twitter')]
    public function shareit($id, Request $request, QuestionRepository $repo): Response
    {
        $publication = $repo->find($id);

        $hashtag = "#" . str_replace(' ', '', $publication->getQuestion());
        $homepageUrl = "http://127.0.0.1:8000/";
        $shareUrl = "https://twitter.com/intent/tweet?url=" . urlencode($homepageUrl);
        $shareUrl .= "&text=" . urlencode($publication->getQuestion());
        $shareUrl .= "&hashtags=" . urlencode($hashtag);

        return $this->redirect($shareUrl);
    }

    #[Route('/search', name: 'search_question')]

    public function search(Request $request, QuestionRepository $repository, ReponseRepository $reponseRepository, PaginatorInterface $paginator): Response
    {


        $query = $request->query->get('query');
        $publications = $repository->findBySearchQuery($query);
        $publications = $paginator->paginate($publications, $request->query->getInt('page', 1), 2);


        return $this->render('question/index.html.twig', [
            'query' => $query,
            'questions' => $publications,   'reponses' => $reponseRepository->findAll(),

        ]);
    }

    #[Route('/stat_forum', name: 'stat_forum', methods: ['GET'])]

    public function stats(QuestionRepository $questionRepository,ReponseRepository $reponseRepository)
    {  // Get the total number of questions and responses
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
    
        // Render the chart
        return $this->render('question/verif.html.twig', [
            'chartData' => json_encode($chartData)
        ]);
    }
    
}
