<?php

namespace App\Form;

use App\Entity\Site;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FiltreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('site', EntityType::class, [
                'class'=>Site::class,
                'placeholder'=>"Choisir un site",
                'choice_label'=>function(Site $site){
                    return $site->getLibelle();
                }
            ])
            ->add('nom')
            ->add('dateDebut', DateType::class, [
                'widget' => 'single_text'
            ])
            ->add('dateCloture', DateType::class, [
                'widget' => 'single_text'
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
