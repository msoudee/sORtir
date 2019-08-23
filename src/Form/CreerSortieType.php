<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreerSortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')

            ->add('dateDebut',DateTimeType::class, [
                'widget' =>'single_text'
            ])
            ->add('duree')
            ->add('dateCloture',DateTimeType::class, [
                'widget' =>'single_text'
            ])
            ->add('nbinscriptionsmax')
            ->add('description',TextareaType::class)
            ->add('lieu', EntityType::class, [
                'class'=>Lieu::class,
                'placeholder'=>"- Choisir un lieu -",
                'choice_label'=>function(Lieu $lieu){
                    return $lieu->getNom();
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
