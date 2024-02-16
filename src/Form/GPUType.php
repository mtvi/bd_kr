<?php

namespace App\Form;

use App\Entity\GPU;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class GPUType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Name', TextType::class)
            ->add('ReleaseDate', DateType::class)
            ->add('CoreClock')
            ->add('BoostClock')
            ->add('VRAM')
            ->add('TDP')
            ->add('Manufacturer', EntityType::class, [
                'class' => 'App\Entity\Manufacturers',
                'choice_label' => 'ManufacturerName',
            ])
            ->add('Memory', EntityType::class, [
                'class' => 'App\Entity\MemoryTypes',
                'choice_label' => 'Type',
            ])
            ->add('PCIVersion', EntityType::class, [
                'class' => 'App\Entity\PCI',
                'choice_label' => 'Version',
            ])
            ->add('Category', EntityType::class, [
                'class' => 'App\Entity\Categories',
                'choice_label' => 'CategoryName',
            ])
            ->add('save', SubmitType::class, ['label' => 'Create Product']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => GPU::class,
        ]);
    }
}
