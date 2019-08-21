<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Form\UserModifyType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //    $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }

    /**
     * @Route("/modify", name="app_modify")
     */

    public function modify(Request $request)
    {
        $user = $this->getUser();
        $form = $this->createForm(UserModifyType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
//            $user->setPassword(
//                $passwordEncoder->encodePassword(
//                    $user,
//                    $form->get('plainPassword')->getData()
//                )
//            );
            if(!is_null($form->get('prenom')->getData())){
                $user->setPrenom($form->get('prenom')->getData());
            }
            if(!is_null($form->get('nom')->getData())){
               // $user->setNom($form->get('nom')->getData());
            }
            if(!is_null($form->get('telephone')->getData())){
                $user->setTelephone($form->get('telephone')->getData());
            }
            if(is_null($form->get('mail')->getData()) != true){
                $user->setMail($form->get('mail')->getData());
            }
            if(is_null($form->get('site')->getData()) != true){
                $user->setSite($form->get('site')->getData());
            }
            $user->setActif(true);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_modify');
        }

        return $this->render('security/modify.html.twig', [
            'modifyForm' => $form->createView(),
        ]);
    }

}
