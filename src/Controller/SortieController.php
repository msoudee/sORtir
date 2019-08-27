<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Inscription;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\User;
use App\Entity\Ville;
use App\Form\AnnulerSortieType;
use App\Form\FiltreType;
use App\Form\NouveauLieuType;
use DateTime;
use App\Form\CreerSortieType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * @param Request $request
     * @return Response
     */
    public function lister(Request $request)
    {
        // Récupération de toutes les sorties enregistrées en BDD
        $sorties = $this->getSorties();
        $sorties = $this->completerDonnesSorties($sorties);

        // Création du formulaire pour filtrer les recherches
        $filtre = new Sortie();
        $filtre->setCbOrganisateur(true);
        $filtre->setCbInscrit(true);
        $filtre->setCbNonInscrit(true);
        $filtre->setCbTerminees(true);

        $formFiltre = $this->createForm(FiltreType::class, $filtre);
        $formFiltre->handleRequest($request);
        if ($formFiltre->isSubmitted()) {
            if (!is_null($filtre->getSite())) {
                foreach ($sorties as $sortie) {
                    if ($sortie->getSite() != $filtre->getSite()) {
                        unset($sorties[array_search($sortie, $sorties)]);
                    }
                }
            }
            if (!is_null($filtre->getNom())) {
                foreach ($sorties as $sortie) {
                    if (strpos($sortie->getNom(), $filtre->getNom()) === false) {
                        unset($sorties[array_search($sortie, $sorties)]);
                    }
                }
            }
            if (!is_null($filtre->getDateDebut())) {
                foreach ($sorties as $sortie) {
                    if ($sortie->getDateDebut() < $filtre->getDateDebut()) {
                        unset($sorties[array_search($sortie, $sorties)]);
                    }
                }
            }
            if (!is_null($filtre->getDateCloture())) {
                foreach ($sorties as $sortie) {
                    if ($sortie->getDateDebut() > $filtre->getDateCloture()) {
                        unset($sorties[array_search($sortie, $sorties)]);
                    }
                }
            }
            if (!$filtre->getCbOrganisateur()) {
                foreach ($sorties as $sortie) {
                    if ($sortie->getOrganisateur() == $this->getUser()) {
                        unset($sorties[array_search($sortie, $sorties)]);
                    }
                }
            }
            if (!$filtre->getCbInscrit()) {
                foreach ($sorties as $sortie) {
                    if ($sortie->getInscrit()) {
                        unset($sorties[array_search($sortie, $sorties)]);
                    }
                }
            }
            if (!$filtre->getCbNonInscrit()) {
                foreach ($sorties as $sortie) {
                    if (!$sortie->getInscrit()) {
                        unset($sorties[array_search($sortie, $sorties)]);
                    }
                }
            }
            if (!$filtre->getCbTerminees()) {
                foreach ($sorties as $sortie) {
                    if ($sortie->getEtat()->getLibelle() == 'Passée') {
                        unset($sorties[array_search($sortie, $sorties)]);
                    }
                }
            }
        }

        return $this->render('sortie/sortie_lister.html.twig', [
            "formFiltre" => $formFiltre->createView(),
            "dateDuJour" => $this->getDateActuelle(),
            "participant" => $this->getNomUser(),
            "sorties" => $sorties
        ]);
    }

    private function getDateActuelle()
    {
        $dateDuJour = new DateTime();
        return $dateDuJour->format("d/m/Y");
    }

    private function getNomUser()
    {
        $userConnecte = $this->getUser();

        if (!is_null($userConnecte)) {
            if (strlen($userConnecte->getNom()) > 0 and strlen($userConnecte->getPrenom()) > 0) {
                $userConnecte = $userConnecte->getPrenom() . ' ' . substr($userConnecte->getNom(), 0, 1) . '.';
            } else {
                $userConnecte = $userConnecte->getUsername();
            }
        }
        return $userConnecte;
    }

    private function getSorties()
    {
        $em = $this->getDoctrine()->getManager();
        $repoSortie = $em->getRepository(Sortie::class);

        $dateLimit = new DateTime();
        $dateLimit->modify("-1 month");

        // Récupération de toutes les sorties de moins d'un mois classé par date de cloture
        $sorties = $repoSortie->findByDateLimitOrderByDateCloture($dateLimit);

        return $sorties;
    }

    private function completerDonnesSorties($sorties)
    {
        foreach ($sorties as $sortie) {
            $em = $this->getDoctrine()->getManager();
            $repoInscription = $em->getRepository(Inscription::class);
            $tmp = $repoInscription->findBy(["sortie" => $sortie->getId()]);

            // Enregistrement du nombre d'inscrit par sorties
            $sortie->setNbInscriptions(sizeof($tmp));

            // Vérification sur les sorties si l'utilisateur courant est inscrit
            $sortie->setInscrit(false);
            foreach ($tmp as $inscription) {
                if ($inscription->getSortie() == $sortie and $inscription->getParticipant() == $this->getUser()) {
                    $sortie->setInscrit(true);
                }
            }

            // Paramètrage des actions en fonction de l'état de la sortie et de l'utilisateur
            // Si l'utilisateur courant est l'organisateur
            if ($sortie->getOrganisateur() == $this->getUser()) {
                switch ($sortie->getEtat()->getLibelle()) {
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
            } // Si l'utilisateur est inscrit
            else if ($sortie->getInscrit()) {
                switch ($sortie->getEtat()->getLibelle()) {
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
            } // Si l'utilisateur n'est pas inscrit
            else if (!$sortie->getInscrit()) {
                switch ($sortie->getEtat()->getLibelle()) {
                    case "Ouverte":
                    case "Clôturée":
                        if ($sortie->getNbInscriptions() < $sortie->getNbInscriptionsMax()) {
                            $sortie->setActions(["afficher", "inscrire"]);
                        } else {
                            $sortie->setActions(["afficher"]);
                        }
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

        $dateDeFin = new DateTime();
        $dateDeFin->modify("+1 day");

        $sortie->setDateCloture($dateDeFin);
        $sortieForm = $this->createForm(CreerSortieType::class, $sortie);
        $sortieForm->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        $repoEtat = $em->getRepository(Etat::class);




        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            $idLieu = $request->get('lieux');

            $lieu = $em->getRepository(Lieu::class)->find($idLieu);
            $sortie->setLieu($lieu);

            $etat = null;
            if ($sortieForm->get("publier")->isClicked()) {

                $etat = $repoEtat->find(2);

            }

            if ($etat == null) {
                $etat = $repoEtat->find(1);
            }


            $user = $this->getUser();
            $site = $user->getSite();
            $sortie->setSite($site);
            $sortie->setEtat($etat);

            $sortie->setOrganisateur($user);
            $em->persist($sortie);
            $em->flush();
            $this->addFlash("messageSuccess", "Votre sortie a bien été enregistrée");
            return $this->redirectToRoute("sortie_lister");
        }

        $lieu = new Lieu();
        $formNouveauLieu = $this->createForm(NouveauLieuType::class, $lieu);

        return $this->render('sortie/sortie_creer.html.twig', [
            'form_CreerSortie' => $sortieForm->createView(),
            'formNouveauLieu' => $formNouveauLieu->createView()
        ]);
    }



    /**
     * @param $idSortie
     *
     * @param Request $request
     * @return Response
     * @Route("/modifier/{idSortie}", name="sortie_modifier")
     */
    public function modifier($idSortie, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $sortie = $em->getRepository(Sortie::class)->find($idSortie);

        $sortieForm = $this->createForm(CreerSortieType::class, $sortie);

        $sortieForm->handleRequest($request);

        $repoEtat = $em->getRepository(Etat::class);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {

            $etat = null;
            if ($sortieForm->get("publier")->isClicked()) {
                $etat = $repoEtat->find(2);
            }

            if ($etat == null) {
                $etat = $repoEtat->find(1);
            }


            $user = $this->getUser();
            $site = $user->getSite();
            $sortie->setSite($site);
            $sortie->setEtat($etat);

            //$sortie->setOrganisateur($user);
            $em->persist($sortie);
            $em->flush();
            $this->addFlash("messageSucces", "Votre sortie a bien été enregistrée");
            return $this->redirectToRoute("sortie_lister");
        }

        return $this->render('sortie/sortie_modifier.html.twig', [
            'form_modifier_sortie' => $sortieForm->createView(),
            'idSortie' => $sortie->getId()]);
    }

    /**
     * @param $idSortie
     *
     * @param Request $request
     * @return Response
     * @Route("/publier/{idSortie}", name="sortie_publier")
     */
    public function publier($idSortie)
    {
        $em = $this->getDoctrine()->getManager();

        $sortie = $em->getRepository(Sortie::class)->find($idSortie);

        $etat = $em->getRepository(Etat::class)->find(2);

        $sortie->setEtat($etat);

        $em->persist($sortie);
        $em->flush();
        $this->addFlash("messageSucces", "Votre sortie a bien été publiée");
        return $this->redirectToRoute("sortie_lister");
    }

    /**
     * @param $idSortie
     *
     * @param Request $request
     * @return Response
     * @Route("/afficher/{id}", name="sortie_afficher")
     */
    public function afficher($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $sortie = $em->getRepository(Sortie::class)->find($id);

        $participants = $em->getRepository(Inscription::class)->findBy(['sortie' => $id]);

        return $this->render('sortie/sortie_afficher.html.twig', ['sortie' => $sortie, "participants" => $participants]);
    }

    /**
     * @param $id
     * @param Request $request
     * @return Response
     * @Route("/annuler/{id}", name="sortie_annuler")
     */
    public function annuler($id, Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        $sortie = $em->getRepository(Sortie::class)->find($id);

        $sortieForm = $this->createForm(AnnulerSortieType::class, $sortie);

        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted()) {

            $etat = $em->getRepository(Etat::class)->find(6);

            $sortie->setEtat($etat);

            $em->persist($sortie);
            $em->flush();
            $this->addFlash("messageSuccess", "Votre sortie a bien été annulée");

            return $this->redirectToRoute("sortie_lister");
        }

        return $this->render('sortie/sortie_annuler.html.twig', ['form_annulerSortie' => $sortieForm->createView(), 'sortie' => $sortie]);
    }

    /**
     * @param $id
     * @return Response
     * @Route("/supprimer/{id}", name="sortie_supprimer")
     */
    public function supprimer($id)
    {
        $em = $this->getDoctrine()->getManager();

        $sortie = $em->getRepository(Sortie::class)->find($id);

        $participants = $em->getRepository(Inscription::class)->findBy(['sortie' => $id]);

        if ($participants) {
            $etat = $em->getRepository(Etat::class)->find(6);
            $sortie->setEtat($etat);
            $em->persist($sortie);
            $em->flush();
            $this->addFlash("messageSuccess", "Votre sortie a bien été annulée");
        } else {
            $em->remove($sortie);
            $em->flush();
            $this->addFlash("messageSuccess", "Votre sortie a bien été supprimée");
        }
        return $this->redirectToRoute("sortie_lister");


    }

    /**
     * @Route("/ajax_lieu", name="sortie_ajax")
     */
    public function ajaxAction(Request $request)
    {
        /* on récupère la valeur envoyée par la vue */
        $idLieu = $request->get('nomLieu');

        $em = $this->getDoctrine()->getManager();
        $repoLieu = $em->getRepository(Lieu::class);
        $lieu = $repoLieu->find($idLieu);
        $rue = $lieu->getRue();
        $latitude = $lieu->getLatitude();
        $longitude = $lieu->getLongitude();
        $ville = $lieu->getVille();
        $nomVille = $ville->getNom();
        $codePostal = $ville->getCodePostal();


        /* la réponse doit être encodée en JSON ou XML, on choisira le JSON
         * la doc de Symfony est bien faite si vous devez renvoyer un objet         *
         */
        $response = new Response(json_encode(array(
            'rue' => $rue,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'nomVille' => $nomVille,
            'codePostal' => $codePostal
        )));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/ajax_ville", name="ville_ajax")
     */
    public function ajaxVilleAction(Request $request)
    {
        /* on récupère la valeur envoyée par la vue */
        $idVille = $request->get('nomVille');

        $em = $this->getDoctrine()->getManager();
        $repoLieu = $em->getRepository(Lieu::class);
        $lieux = $repoLieu->findBy(["ville" => $idVille]);;

        /* la réponse doit être encodée en JSON ou XML, on choisira le JSON
         * la doc de Symfony est bien faite si vous devez renvoyer un objet         *
         */
        return new JsonResponse($lieux);
//        $response->headers->set('Content-Type', 'application/json');

  //      return $response;
    }

    /**
     * @Route("/ajax_nouveau_lieu", name="ajax_nouveau_lieu")
     */
    public function ajaxNouveauLieu(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repoVille = $em->getRepository(Ville::class);

        $lieu = new Lieu();
        $ville = $repoVille->find($request->get('ville'));

        $lieu->setNom($request->get('nom'));
        $lieu->setVille($ville);
        $lieu->setRue($request->get('rue'));
        $lieu->setLatitude($request->get('lat'));
        $lieu->setLongitude($request->get('long'));

        $em->persist($lieu);
        $em->flush();

        $response = new Response(json_encode(array(
            'newVille' => $lieu->getVille()->getId(),
            'newLieu' => $lieu->getNom()
        )));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }


}
