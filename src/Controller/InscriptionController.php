<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Inscription;
use App\Entity\Sortie;
use App\Form\CreerSortieType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/inscription")
 */
class InscriptionController extends AbstractController
{
    /**
     * @Route("/inscrire/{id}", name="inscription_inscrire")
     * @param $id
     * @param $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function inscrire($id)
    {
        $inscription = new Inscription();
        $inscription->setDateInscription(new \DateTime());

        $em = $this->getDoctrine()->getManager();

        $dejaInscrit =$em->getRepository(Inscription::class)->findOneBy(["sortie"=>$id, "participant" =>$this->getUser()->getId()]);

        $sortie = $em->getRepository(Sortie::class)->find($id);

        if(!$dejaInscrit) {

            $inscription->setSortie($sortie);
            $inscription->setParticipant($this->getUser());

            $em->persist($inscription);
            $em->flush();
            $this->addFlash("messageSuccess", "Vous êtes inscrit à la sortie " . $sortie->getNom());
        }else{
            $this->addFlash("messageErreur", "Vous êtes déjà inscrit à la sortie " . $sortie->getNom());
        }

        return $this->redirectToRoute("sortie_lister");


    }

    /**
     * @Route("/desiste/{id}", name="inscription_desister")
     * @param $id
     * @param $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function desister($id)
    {
        $inscription = new Inscription();
        $inscription->setDateInscription(new \DateTime());

        $em = $this->getDoctrine()->getManager();

        $dejaInscrit =$em->getRepository(Inscription::class)->findOneBy(["sortie"=>$id, "participant" =>$this->getUser()->getId()]);

        $sortie = $em->getRepository(Sortie::class)->find($id);

        if($dejaInscrit) {

          $em->remove($dejaInscrit);

            $em->flush();
            $this->addFlash("messageSuccess", "Vous n'êtes  plus inscrit à la sortie " . $sortie->getNom());
        }

        return $this->redirectToRoute("sortie_lister");


    }
}
