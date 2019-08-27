<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreerSortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class,[
                'label'=>'Nom de la sortie'
            ])
            ->add('dateDebut',DateTimeType::class, [
                'widget' =>'single_text',
                'label'=>'Date'
            ])
            ->add('duree', TextType::class,[
                'label'=>'Durée de la sortie'
            ])
            ->add('dateCloture',DateTimeType::class, [
                'widget' =>'single_text',
                'label'=>"Date limite d'inscription"
            ])
            ->add('nbinscriptionsmax', TextType::class,[
                'label'=>'Nombre maximum de participants'
            ])
            ->add('description',TextareaType::class, [
                'label' => 'Infomations et description'
            ])
            ->add('ville', EntityType::class, [
                'class'=>Ville::class,
                'mapped' => false,
                'label' => 'Ville',
                'placeholder'=>"- Choisir une ville -",
                'choice_label'=>function(Ville $ville){
                    return $ville->getNom();
                }
            ])
            ->add('enregistrer', SubmitType::class, [
                'attr' => ['class' => 'btn-block btn-primary btn'],
            ])
            ->add('publier', SubmitType::class, [
                'attr' => ['class' => 'btn-block btn-success btn'],
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
