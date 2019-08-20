<?php

namespace App\Controller;

use DateTime;
use App\Entity\Sorties;
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
    public function lister()
    {
        $dateDuJour = new DateTime();
        $dateDuJour = $dateDuJour->format("d/m/Y");
        $participant = "Maxence S.";

        return $this->render('sortie/sortie_lister.html.twig', ["dateDuJour"=>$dateDuJour, "participant"=>$participant]);
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
