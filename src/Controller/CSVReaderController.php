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
        $target_dir = "C:/wamp64/www/sORtir/src/Data/";
        $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        // Check if image file is a actual image or fake image
        if(isset($_POST["submit"])) {
            //$check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
            if($imageFileType != "csv"){
                $this->addFlash('notice', "Sorry, only CSV files are allowed.");
                $uploadOk = 0;
            }
            // Check if $uploadOk is set to 0 by an error
            if ($uploadOk == 0) {
                $this->addFlash('notice', "Sorry, your file was not uploaded.");
            // if everything is ok, try to upload file
            } else {
                if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                    //Open the file.
                    $fileHandle = fopen("C:\wamp64\www\sORtir\src\Data/".$_FILES["fileToUpload"]["name"] , "r");
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
                    $this->addFlash('notice', "Le fichier ". basename( $_FILES["fileToUpload"]["name"]). " a été upload.");
                } else {
                    $this->addFlash('notice', "Sorry, there was an error uploading your file.");
                }
            }


        }



        return $this->redirectToRoute('app_users');
    }
}
