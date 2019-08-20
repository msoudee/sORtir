<?php

namespace App\Controller;

use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
}
