<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Inscription;
use App\Entity\Lieu;
use App\Entity\Site;
use App\Entity\Sortie;
use App\Entity\User;
use App\Form\FiltreType;
use DateTime;
use App\Form\CreerSortieType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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

        if(!is_null($userConnecte)) {
            if (strlen($userConnecte->getNom()) > 0 and strlen($userConnecte->getPrenom()) > 0) {
                $userConnecte = $userConnecte->getPrenom() . ' ' . substr($userConnecte->getNom(), 0, 1) . '.';
            } else {
                $userConnecte = $userConnecte->getUsername();
            }
        }
        return $userConnecte;
    }

    private function getSorties(){
        $em = $this->getDoctrine()->getManager();
        $repoSortie = $em->getRepository(Sortie::class);
        $repoInscription = $em->getRepository(Inscription::class);

        // Récupération de toutes les sorties
        $sorties = $repoSortie->findAll();

        foreach ($sorties as $sortie) {
            $tmp = $repoInscription->findBy(["sortie"=>$sortie->getId()]);

            // Enregistrement du nombre d'inscrit par sorties
            $sortie->setNbInscriptions(sizeof($tmp));

            // Vérification sur les sorties si l'utilisateur courant est inscrit
            $sortie->setInscrit(false);
            foreach ($tmp as $inscription){
                if($inscription->getSortie() == $sortie and $inscription->getParticipant() == $this->getUser()){
                    $sortie->setInscrit(true);
                }
            }

            // Paramètrage des actions en fonction de l'état de la sortie et de l'utilisateur
            // Si l'utilisateur courant est l'organisateur
            if($sortie->getOrganisateur() == $this->getUser()) {
                switch ($sortie->getEtat()->getLibelle()){
                    case "Ouverte":
                    case "Clôturée":
                        $sortie->setActions(["afficher", "annuler"]);
                        break;
                    case "En cours":
                    case "Passée":
                    case "Annulée":
                        $sortie->setActions(["afficher"]);
                        break;
                    default :
                        $sortie->setActions(["modifier", "publier"]);
                        break;
                }
            }
            // Si l'utilisateur est inscrit
            else if ($sortie->getInscrit()) {
                switch ($sortie->getEtat()->getLibelle()){
                    case "Ouverte":
                    case "Clôturée":
                        $sortie->setActions(["afficher", "desister"]);
                        break;
                    case "En création":
                        $sortie->setActions([]);
                        break;
                    default :
                        $sortie->setActions(["afficher"]);
                        break;
                }
            }
            // Si l'utilisateur n'est pas inscrit
            else if(!$sortie->getInscrit()) {
                switch ($sortie->getEtat()->getLibelle()){
                    case "Ouverte":
                    case "Clôturée":
                        $sortie->setActions(["afficher", "inscrire"]);
                        break;
                    case "En création":
                        $sortie->setActions([]);
                        break;
                    default :
                        $sortie->setActions(["afficher"]);
                        break;
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

    /**
     * @Route("/ajax_lieu", name="sortie_ajax")
     */
    public function ajaxAction(Request $request)
    {
        /* on récupère la valeur envoyée par la vue */
        $idLieu = $request->request->get('nomLieu');

        $em = $this->getDoctrine()->getManager();
        $repoLieu = $em->getRepository(Lieu::class);
        $lieu = $repoLieu->find($idLieu);
        $rue = $lieu->getRue();
        $latitude = $lieu->getLatitude();
        $longitude = $lieu->getLongitude();
        $ville = $lieu->getVille();
        $nomVille =$ville->getNom();
        $codePostal = $ville->getCodePostal();

        /* la réponse doit être encodée en JSON ou XML, on choisira le JSON
         * la doc de Symfony est bien faite si vous devez renvoyer un objet         *
         */
        $response = new Response(json_encode(array(
            'rue' => $rue,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'nomVille' => $nomVille,
            'codePostal' =>$codePostal
        )));

        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

}
