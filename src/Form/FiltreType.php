<?php

namespace App\Form;

use App\Entity\Sites;
use App\Entity\Sorties;
use Doctrine\DBAL\Types\StringType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FiltreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('siteOrganisateur', EntityType::class, [
                'class'=>Sites::class,
                'placeholder'=>"Choisir un site organisateur",
                'choice_label'=>function(Sites $sites){
                    return $sites->getNomSite();
                }
            ])
            ->add('nom')
            ->add('dateDebut')
            ->add('dateCloture')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sorties::class,
        ]);
    }
}
