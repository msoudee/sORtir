<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NouveauLieuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class,[
                'label'=>'Nom à donner au lieu'
            ])
            ->add('rue', TextType::class,[
                'label'=>'Nom de la rue'
            ])
            ->add('latitude', TextType::class,[
                'label'=>'Latitude'
            ])
            ->add('longitude', TextType::class,[
                'label'=>'Longitude'
            ])
            ->add('ville', EntityType::class, [
                'class'=>Ville::class,
                'label' => 'Ville',
                'placeholder'=>"Choisir une ville",
                'choice_label'=>function(Ville $ville){
                    return $ville->getNom();
                }
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Lieu::class,
        ]);
    }
}
