<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Form\EnregistrerVilleType;
use App\Form\RechercherVilleType;
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

        $villes = $repoVille->findAll();

        $searchCity = new Ville();
        $searchCityForm = $this->createForm(RechercherVilleType::class, $searchCity);
        $searchCityForm->handleRequest($request);
        if ($searchCityForm->isSubmitted()) {

            return $this->redirectToRoute("gestion_ville");
        }

        $newCity = new Ville();
        $newCityForm = $this->createForm(EnregistrerVilleType::class, $newCity);
        $newCityForm->handleRequest($request);
        if ($newCityForm->isSubmitted()) {
            $em->persist($newCity);
            $em->flush();
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

        return $this->redirectToRoute("gestion_ville");
    }
}
