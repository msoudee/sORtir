<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Site;
use App\Entity\Sortie;
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

        $em = $this->getDoctrine()->getManager();
        $repoSortie = $em->getRepository(Sortie::class);

        $sorties = $repoSortie->findAll();

        $filtre = new Sortie();
        $formFiltre = $this->createForm(FiltreType::class, $filtre);

        $formFiltre->handleRequest($request);
        if ($formFiltre->isSubmitted() && $formFiltre->isValid()) {

        }

        return $this->render('sortie/sortie_lister.html.twig', [
            "formFiltre" => $formFiltre->createView(),
            "dateDuJour" => $dateDuJour->format("d/m/Y"),
            "participant" => $this->getUser()->getUserName(),
            "sorties" => $sorties
        ]);
    }

    /**
     * @Route("/creer", name="sortie_creer")
     */
    public function creer(Request $request)
    {

        $sortie = new Sortie();
        $sortie->setDateDebut(new \DateTime());
        $sortie->setDateCloture(new \DateTime());
        $sortieForm = $this->createForm(CreerSortieType::class, $sortie);
        $sortieForm->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        $repoEtat = $em->getRepository(Etat::class);
        $repoSite = $em->getRepository(Site::class);

        if ($sortieForm->isSubmitted()) {
            $etat = $repoEtat->find(1);

            $user = $this->getUser();
            $site = $user->getSite();
            $sortie->setSite($site);
            $sortie->setEtat($etat);
            $
            $sortie->setOrganisateur($user);
            $em->persist($sortie);
            $em->flush();
            $this->addFlash("messageSucces", "Votre sortie a bien été enregistrée");
            return $this->redirectToRoute("sortie_lister");
        }

        return $this->render('sortie/sortie_creer.html.twig', ['form_CreerSortie' => $sortieForm->createView()]);
    }


}
