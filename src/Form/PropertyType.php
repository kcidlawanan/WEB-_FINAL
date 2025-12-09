<?php

namespace App\Form;

use App\Entity\Property;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints as Assert;

class PropertyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class)
            ->add('description', TextareaType::class)
            ->add('price', NumberType::class, [
                'html5' => true,
                'scale' => 2,
                'attr' => [
                    'min' => 0,
                    'step' => '0.01',
                    'inputmode' => 'decimal',
                    'pattern' => '[0-9]+(\.[0-9]{1,2})?'
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Please enter a price.']),
                    new Assert\PositiveOrZero(['message' => 'Price must be zero or positive.']),
                    new Assert\Regex([
                        'pattern' => '/^[0-9]+(\.[0-9]{1,2})?$/',
                        'message' => 'Enter a valid price (numbers only, up to 2 decimal places).'
                    ])
                ]
            ])
            ->add('location', TextType::class)
            ->add('bedrooms', IntegerType::class, [
                'attr' => [
                    'min' => 0,
                    'step' => 1,
                    'inputmode' => 'numeric',
                    'pattern' => '[0-9]*'
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Please enter number of bedrooms.']),
                    new Assert\PositiveOrZero(['message' => 'Bedrooms must be zero or positive.']),
                    new Assert\Type(['type' => 'integer', 'message' => 'Bedrooms must be an integer.'])
                ]
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'placeholder' => 'Select Category',
                'required' => false,
            ])
            ->add('image', FileType::class, [
                'label' => 'Upload Property Image',
                'mapped' => false, // important: not linked directly to entity field
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => ['image/jpeg', 'image/png', 'image/jpg'],
                        'mimeTypesMessage' => 'Please upload a valid image file (JPEG or PNG)',
                    ])
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Property::class,
        ]);
    }
}
