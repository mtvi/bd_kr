<?php

// src/Form/ProductType.php

namespace App\Form;

use App\Entity\Products;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ProductFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('price')
            ->add('coolingType', TextType::class)
            ->add('gpu', EntityType::class, [
                'class' => 'App\Entity\GPU', // Update with the actual GPU entity class
                'choice_label' => 'name', // Display GPU names in the dropdown
            ])
            ->add('vendor', EntityType::class, [
                'class' => 'App\Entity\Vendors', // Update with the actual Vendor entity class
                'choice_label' => 'name', // Display vendor names in the dropdown
            ])
            ->add('image', FileType::class, [
                'label' => 'Product Image',
                'required' => false,
                'mapped' => false, 
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'image/*',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image file.',
                    ]),
                ],
            ])
            ->add('quantity')
            ->add('Views')
            ->add('save', SubmitType::class, ['label' => 'Save']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Products::class,
        ]);
    }
}
