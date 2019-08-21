<?php

namespace App\Controller;

use App\Entity\Sorties;
use App\Form\FiltreType;
use DateTime;
use App\Form\CreerSortieType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/sortie")
 */
class SortieController extends AbstractController
{
    /**
     * @Route("/lister", name="sortie_lister")
     */
    public function lister(Request $request)
    {
        $dateDuJour = new DateTime();
        $dateDuJour = $dateDuJour->format("d/m/Y");
        $participant = "Maxence S.";

        $filtre = new Sorties();
        $formFiltre = $this->createForm(FiltreType::class, $filtre);

        $formFiltre->handleRequest($request);
        if($formFiltre->isSubmitted() && $formFiltre->isValid()){

        }

        return $this->render('sortie/sortie_lister.html.twig', ["formFiltre"=>$formFiltre->createView(), "dateDuJour"=>$dateDuJour, "participant"=>$participant]);
    }

    /**
     * @Route("/creer", name="sorite_creer")
     */
    public function creer(EntityManagerInterface $em, Request $request)
    {

        $sortie = new Sorties();

        $sortieForm = $this->createForm(CreerSortieType::class, $sortie);
        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted()) {


            $em->persist($sortie);
            $em->flush();
            $this->addFlash("messageSucces", "Votre sortie a bien été enregistrée");
            return $this->redirectToRoute("sortie_lister");
        }

        return $this->render('sortie/sortie_creer.html.twig', ['form_CreerSortie' => $sortieForm->createView()]);
    }


}
