<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="main_accueil")
     */
    public function accueil() {
        if($this->getUser()) {
            return $this->redirectToRoute('sortie_lister');
        }
        return $this->render('main/accueil.html.twig');
    }
}
