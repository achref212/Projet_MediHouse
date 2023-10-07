<?php

namespace App\Controller;

use App\Entity\Reclamation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ReclamationRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use App\Form\ReclamationType;
use App\Entity\Reponse1;
use App\Form\Reponse1Type;
use App\Repository\Reponse1Repository;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
///pdf///
use TCPDF;
use Dompdf\Dompdf;
use Dompdf\Options;
use phpDocumentor\Reflection\PseudoTypes\True_;

class ReclamationController extends AbstractController
{

  #[Route('/pdf/{id}', name: 'app_PDF', methods: ['GET'])]
  public function Pdf(ReclamationRepository $ReclamationRepository, $id, Reponse1Repository $Reponse1Repository): Response
  {

    $pdfOptions = new Options();
    $pdfOptions->set('isRemoteEnabled', true);
    $pdfOptions->set('defaultPaperSize', 'A4');
    $pdfOptions->set('defaultPaperOrientation', 'portrait');
    $pdfOptions->set('defaultFont', 'Arial');
    $pdfOptions->set('margin-top', '1cm');
    $pdfOptions->set('margin-right', '1cm');
    $pdfOptions->set('margin-bottom', '1cm');
    $pdfOptions->set('margin-left', '1cm');


    // create new PDF document
    $dompdf = new Dompdf($pdfOptions);
    // set the paper size and orientation
    $dompdf->setPaper('A4', 'portrait');

    $html = $this->renderView('PDF/pdf.html.twig', [

      'reclamation' => $ReclamationRepository->find($id),
      'Reponse1' => $Reponse1Repository->get_Reponse1($id),
    ]);
    // set the HTML content

    // load the HTML into the document
    $dompdf->loadHtml($html);



    // render the HTML as PDF
    $dompdf->render();

    // get the PDF file contents
    $dompdf->stream("Reclamation.pdf", [
      'Attachment' => false
    ]);



    return new Response('test');
  }
  #[Route('/afficheREC', name: 'afficheR')]

  public function AfficheR(ReclamationRepository $repository)
  {
    $this->denyAccessUnlessGranted('ROLE_ADMIN');
    $c = $repository->findAll();
    return $this->render("reclamation/listR.html.twig", ["reclamations" => $c]);
  }
  #[Route('/afficheRECl', name: 'afficheRl')]

  public function AfficheRl(ReclamationRepository $repository)
  {
    $c = $repository->findAll();
    return $this->render("reclamation/addrec.html.twig", ["reclamation" => $c]);
  }

  #[Route('/affREC/{id}', name: 'affR')]

  public function AffR(ReclamationRepository $repository, $id, Reponse1Repository $Reponse1repository)
  {
    $Reclamation = $repository->find($id);
    $r = $Reponse1repository->get_Reponse1($id);
    if ($r == null) {
      return $this->render("reclamation/frontREC1.html.twig", ["reclamation" => $Reclamation]);
    }
    return $this->render("reclamation/frontREC.html.twig", ["reclamation" => $Reclamation, "Reponse1" => $r]);
  }

  #[Route('/addReclamation', name: 'app_addReclamation')]
  public function addReclamation(ManagerRegistry $doctrine, Request $request)
  {
    $Reclamation = new Reclamation();
    //$Reclamation->setDateReclamation()

    $form = $this->createForm(ReclamationType::class, $Reclamation);
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {

      $em = $doctrine->getManager();
      $em->persist($Reclamation);
      $em->flush();
      return $this->redirectToRoute("afficheRl");
    }
    return $this->renderForm(
      "reclamation/front.html.twig",
      array("form" => $form)
    );
  }
  #[Route('/afficheview/{id}', name: 'afficheView')]

  public function Afficheview(ReclamationRepository $repository, $id, Reponse1Repository $Reponse1repository)
  {
    $Reclamation = $repository->find($id);
    $r = $Reponse1repository->get_Reponse1($id);
    if ($r == null) {
      return $this->render("reclamation/View.html.twig", ["reclamation" => $Reclamation]);
    }
    return $this->render("reclamation/View1.html.twig", ["reclamation" => $Reclamation, 'Reponse1' => $r]);
  }



  #[Route('/suppE/{id}', name: 'supprimerR')]

  public function suppC(ManagerRegistry $doctrine, $id, ReclamationRepository $repository)
  {
    //récupérer le classroom à supprimer
    $Reclamation = $repository->find($id);
    //récupérer l'entity manager
    $em = $doctrine->getManager();
    $em->remove($Reclamation);
    $em->flush();
    return $this->redirectToRoute("afficheR");
  }
  #[Route('/updatC/{id}', name: 'updateR')]

  public function updatC(ManagerRegistry $doctrine, $id, ReclamationRepository $repository, Request $request)
  {
    //récupérer le Reclamation à supprimer
    $Reclamation = $repository->find($id);
    $newReclamation = new Reclamation();
    $form = $this->createForm(ReclamationType::class, $Reclamation);
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
      $em = $doctrine->getManager();
      $Reclamation->setEmail($newReclamation->getEmail());
      $Reclamation->setSujet($newReclamation->getSujet());
      $Reclamation->setDescreption($newReclamation->getDescreption());
      // $Reclamation->setDateReclamation($newReclamation->getDateReclamation());
      // $Reclamation->setEtat($newReclamation->getEtat());
      $em->flush();
      return $this->redirectToRoute("afficheRl");
    }
    return $this->renderForm(
      "reclamation/updateR.html.twig",
      array("form" => $form)
    );
  }


  #[Route('/afficheRECj', name: 'afficheRj')]
  public function AfficheRlj(ReclamationRepository $repository, Request $request, SerializerInterface $serializer)
  {
    $rr = $repository->findAll();
    $data = [];

    foreach ($rr as $r) {

      $data[] = [
        'id' => $r->getId(),
        'email' => $r->getEmail(),
        'sujet' => $r->getSujet(),
        'descreption' => $r->getDescreption(),
      ];
    }
    $jason = $serializer->serialize($data, 'json', ['groups' => 'reclamation_list']);
    return new Response($jason);
  }
  #[Route('/addreclamationJSON', name: 'app_addreclamationJSON')]
  public function addreclamationJSON(ManagerRegistry $doctrine, Request $request, SerializerInterface $serializer)
  {

    $reclamation = new Reclamation();
    $reclamation->setEmail($request->get("email"));
    $reclamation->setSujet($request->get("sujet"));
    $reclamation->setDescreption($request->get("description"));
    $datereclamation = new \DateTime($request->get("datereclamation"));
    $reclamation->setDateReclamation($datereclamation);

    $em = $doctrine->getManager();
    $em->persist($reclamation);
    $em->flush();
    $jason = $serializer->serialize($reclamation, 'json', ['groups' => 'reclamation_list']);
    return new Response($jason);
  } //http://127.0.0.1:8000/addreclamationJSON?libelle=mahmoud&description=miboun&image=C:%2FUsers%2FLENOVO%2FDownloads%2Fwassim.jpg&quantite=21
  //http://127.0.0.1:8000/addReservationJSON?email=dhia.hamdi@esprit.tn&sujet=dffdfd&description=fsgdb&datereclamation=2025-01-01T01:01:00Z

}
