<?php

namespace App\Form;

use App\Entity\Reviews;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ReviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Reviewer', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control w-25 mb-2',
                    'placeholder' => 'Your Name',
                ]
            ])
            ->add('Rating', IntegerType::class, [
                'constraints' => [
                    new Range([
                        'min' => 1,
                        'max' => 5,
                        'minMessage' => 'The rating must be at least {{ limit }}',
                        'maxMessage' => 'The rating cannot be greater than {{ limit }}',
                    ]),
                    new NotBlank(['message' => 'Please enter a rating.']),
                ],
                'label' => false,
            ])
            ->add('ReviewText', TextareaType::class, [
                'attr' => [
                    'placeholder' => 'Your Review',
                    'class' => 'mt-2 w-50 form-control',
                    'style' => 'height: 100px',
                ],
                'label' => false,

            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reviews::class,
        ]);
    }
}
