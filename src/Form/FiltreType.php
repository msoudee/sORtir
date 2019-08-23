<?php

namespace App\Form;

use App\Entity\Site;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FiltreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('site', EntityType::class, [
                'class'=>Site::class,
                'required'=>false,
                'placeholder'=>"Choisir un site",
                'choice_label'=>function(Site $site){
                    return $site->getLibelle();
                }
            ])
            ->add('nom', TextType::class, [
                'required'=>false
            ])
            ->add('dateDebut', DateType::class, [
                'required'=>false,
                'widget' => 'single_text',
                'empty_data'=>'-'
            ])
            ->add('dateCloture', DateType::class, [
                'required'=>false,
                'widget' => 'single_text',
                'empty_data'=>'-'
            ])
            ->add("cbOrganisateur", CheckboxType::class, [
                'label'    => "Sorties dont je suis l'organisateur/trice",
                'required' => false
            ])
            ->add("cbInscrit", CheckboxType::class, [
                'label'    => "Sorties auxquelles je suis inscrit/e",
                'required' => false
            ])
            ->add("cbNonInscrit", CheckboxType::class, [
                'label'    => "Sorties auxquelle je ne suis pas inscrti/e",
                'required' => false
            ])
            ->add("cbTerminees", CheckboxType::class, [
                'label'    => "Sorties terminées",
                'required' => false
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
