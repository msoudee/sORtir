<?php

namespace App\Controller;

use App\Entity\Site;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CSVReaderController extends AbstractController
{
    /**
     * @Route("/csvreader", name="csv_reader")
     */
    public function index(UserPasswordEncoderInterface $passwordEncoder)
    {
        //Open the file.
        $fileHandle = fopen("C:\wamp64\www\sORtir\src\Data\users.csv", "r");
        //Loop through the CSV rows.
        $cpt = 0;
        $entityManager = $this->getDoctrine()->getManager();
        while (($row = fgetcsv($fileHandle, 0, ",")) !== FALSE) {
            if($cpt != 0 ){
                $test = $entityManager->getRepository(User::class )->findBy(array('username' => $row[0]));
                if(!$test){
                    $user = new User();
                    $user->setActif(1);
                    $user->setUsername($row[0]);
                    $user->setRoles("ROLE_USER");
                    $user->setPassword(
                        $passwordEncoder->encodePassword(
                            $user,
                            $row[1]
                        )
                    );
                    $user->setMail($row[2]);
                    $user->setNom($row[3]);
                    $user->setPrenom($row[4]);
                    $user->setTelephone($row[5]);
                    $user->setSite($entityManager->find(Site::class, $row[6]));
                    $entityManager->persist($user);
                    $entityManager->flush();


                    var_dump($user);
                }

            }
            $cpt++;
        }

        return $this->redirectToRoute('app_users');
    }
}
