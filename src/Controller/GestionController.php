<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Site;
use App\Entity\Ville;
use App\Form\EnregistrerVilleType;
use App\Form\NewSiteType;
use App\Form\NouveauLieuType;
use App\Form\RechercherVilleType;
use App\Form\SearchSiteType;
use App\Form\SiteType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/gestion")
 */
class GestionController extends AbstractController
{
    /**
     * @Route("/villes", name="gestion_ville")
     */
    public function gererLesVilles(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repoVille = $em->getRepository(Ville::class);

        $villes = $repoVille->findAllOrdererByName();

        $searchCity = new Ville();
        $searchCityForm = $this->createForm(RechercherVilleType::class, $searchCity);
        $searchCityForm->handleRequest($request);
        if ($searchCityForm->isSubmitted()) {
            foreach ($villes as $ville) {
                if(strpos(strtolower($ville->getNom()), strtolower($searchCity->getNom())) === false ){
                    unset($villes[array_search($ville, $villes)]);
                }
            }
        }

        $newCity = new Ville();
        $newCityForm = $this->createForm(EnregistrerVilleType::class, $newCity);
        $newCityForm->handleRequest($request);
        if ($newCityForm->isSubmitted()) {
            $em->persist($newCity);
            $em->flush();
            $this->addFlash("messageSuccess", "Ville ajoutée avec succés !");
            return $this->redirectToRoute("gestion_ville");
        }

        return $this->render('gestion/gestion_ville.html.twig', [
            "villes" => $villes,
            "newCityForm" => $newCityForm->createView(),
            "searchCityForm" => $searchCityForm->createView()
        ]);
    }

    /**
     * @Route("/villes/supprimer/{id}", name="gestion_ville_supprimer")
     */
    public function supprimerUneVille($id)
    {
        $em = $this->getDoctrine()->getManager();
        $repoVille = $em->getRepository(Ville::class);

        $ville = $repoVille->find($id);

        $em->remove($ville);
        $em->flush();
        $this->addFlash("messageSuccess", "Ville supprimée avec succés !");

        return $this->redirectToRoute("gestion_ville");
    }

    /**
     * @Route("/sites", name="gestion_site")
     */
    public function gererLesSites(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(Site::class);

        $sites = $repo->findAllOrdererByName();

        $searchSite = new Site();
        $newSite = new Site();

        $searchSiteForm = $this->createForm(SearchSiteType::class, $searchSite);
        $newSiteForm = $this->createForm(NewSiteType::class, $newSite);

        $searchSiteForm->handleRequest($request);
        if ($searchSiteForm->isSubmitted()) {
            foreach ($sites as $site) {
                if(strpos(strtolower($site->getLibelle()), strtolower($searchSite->getLibelle())) === false ){
                    unset($sites[array_search($site, $sites)]);
                }
            }
        }

        $newSiteForm->handleRequest($request);
        if ($newSiteForm->isSubmitted()) {
            $em->persist($newSite);
            $em->flush();
            $this->addFlash("messageSuccess", "Site ajouté avec succés !");
            return $this->redirectToRoute("gestion_site");
        }

        return $this->render('gestion/gestion_site.html.twig', [
            "sites" => $sites,
            "newSiteForm" => $newSiteForm->createView(),
            "searchSiteForm" => $searchSiteForm->createView()
        ]);
    }

    /**
     * @Route("/sites/supprimer/{id}", name="gestion_site_supprimer")
     */
    public function supprimerUnSite($id)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(Site::class);

        $site = $repo->find($id);

        $em->remove($site);
        $em->flush();
        $this->addFlash("messageSuccess", "Site supprimé avec succés !");

        return $this->redirectToRoute("gestion_site");
    }
}
