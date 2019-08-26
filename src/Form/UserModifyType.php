<?php

namespace App\Form;

use App\Entity\Site;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UserModifyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, [
                'label' => 'Pseudo',
                'required' => true
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => false,
                'first_options'  => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Vérification mot de passe'],
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'required' => true
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'required' => true
            ])
            ->add('telephone', TextType::class, [
                'label' => 'Numéro de téléphone',
                'required' => true,
                'constraints'=> [new Length(10)],
            ])
            ->add('mail', EmailType::class, [
                'label' => 'Adresse mail',
                'required' => true
            ])
            ->add('site', EntityType::class, [
                'label' => 'Site',
                'class'=>Site::class,
                'required'=> true,
                'placeholder'=>"Choisir un site",
                'choice_label'=>function(Site $site){
                    return $site->getLibelle();
            }
            ])
            ->add('photo', FileType::class, [
                'label' => 'Ajouter une photo de profil',
                'attr' => ['placeholder' => 'Choisissez votre image'],
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '102400k',

                        'mimeTypesMessage' => 'Le format du fichier n\'est pas valide',
                    ])
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
