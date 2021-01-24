<?php

namespace App\Form;

use App\Entity\Material;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MaterialType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',TextType::class,[
                'label' => 'Název'
            ])
            ->add('size',TextType::class,[
                'label' => 'Velikost'
            ])
            ->add('panelPrice',IntegerType::class,[
                'label' => 'Cena za desku bez DPH'
            ])
            ->add('meterPrice',IntegerType::class,[
                'label' => 'Cena za m2 bez DPH'
            ])
            ->add('save', SubmitType::class,[
                'label' => 'uložit'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Material::class,
        ]);
    }
}
