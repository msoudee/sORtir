<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
/**
 * @Route("/admin")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/users", name="app_users")
     */
    public function index()
    {
        $repository = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository(User::class)
        ;

        $users = $repository->findAll();
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            'users' => $users,
        ]);
    }
    /**
     * @Route("/users/desac/{id}", name="admin_desactiver")
     */
    public function desactivate(int $id){
        $repository = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository(User::class)
        ;
        $entityManager = $this->getDoctrine()->getManager();
        $user = $repository->find($id);
        $user->setActif(0);
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('app_users');
    }

    /**
     * @Route("/users/del/{id}", name="admin_supprimer")
     */
    public function delete(int $id){
        $repository = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository(User::class)
        ;
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($repository->find($id));
        $entityManager->flush();

        return $this->redirectToRoute('app_users');
    }
}
