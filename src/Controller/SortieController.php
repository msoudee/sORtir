<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Inscription;
use App\Entity\Site;
use App\Entity\Sortie;
use App\Entity\User;
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
    public function lister(/*Request $request*/)
    {
        // Récupération de toutes les sorties enregistrées en BDD
        $sorties = $this->getSorties();

        // Création du formulaire pour filtrer les recherches
        $filtre = new Sortie();
        $formFiltre = $this->createForm(FiltreType::class, $filtre);
        /*$formFiltre->handleRequest($request);
        if ($formFiltre->isSubmitted() && $formFiltre->isValid()) {

        }*/

        return $this->render('sortie/sortie_lister.html.twig', [
            "formFiltre" => $formFiltre->createView(),
            "dateDuJour" => $this->getDateActuelle(),
            "participant" => $this->getNomUser(),
            "sorties" => $sorties
        ]);
    }

    private function getDateActuelle(){
        $dateDuJour = new DateTime();
        return $dateDuJour->format("d/m/Y");
    }

    private function getNomUser(){
        $userConnecte = $this->getUser();
        if(strlen($userConnecte->getNom()) > 0 and strlen($userConnecte->getPrenom()) > 0){
            $userConnecte = $userConnecte->getPrenom().' '.substr($userConnecte->getNom(), 0, 1).'.';
        } else {
            $userConnecte = $userConnecte->getUsername();
        }
        return $userConnecte;
    }

    private function getSorties(){
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(Sortie::class);

        // Récupération du nombre d'inscriptions pour chaque sorties récupérées
        return $this->getNombreInscriptions($repo->findAll());
    }

    private function getNombreInscriptions(array $sorties){
        $em = $this->getDoctrine()->getManager();
        $repoInscription = $em->getRepository(Inscription::class);

        foreach ($sorties as $sortie) {
            $tmp = $repoInscription->findBy(["sortie"=>$sortie->getId()]);
            $sortie->setNbInscriptions(sizeof($tmp));
            $sortie->setInscrit(false);

            foreach ($tmp as $inscription){
                if($inscription->getSortie() == $sortie and $inscription->getParticipant() == $this->getUser()){
                    $sortie->setInscrit(true);
                }
            }
        }
        return $sorties;
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
