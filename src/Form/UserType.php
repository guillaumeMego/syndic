<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('prenom', TextType::class, [
                'attr' => [
                    'placeholder' => 'Entrez un prénom',
                    'class' => 'form-control',
                    'minlength' => '2',
                    'maxlength' => '100'
                ],
                'label' => 'Prénom',
                'label_attr' => [
                    'class' => 'form-label mt-2'
                ],
                'constraints' => [
                    new Assert\Length([
                        'min' => 2,
                        'max' => 100,
                    ]),
                    new Assert\NotBlank([
                        'message' => 'Le prénom est obligatoire'
                    ])
                ]
            ])
            ->add('nom', TextType::class, [
                'attr' => [
                    'placeholder' => 'Entrez un nom',
                    'class' => 'form-control',
                    'minlength' => '2',
                    'maxlength' => '100'
                ],
                'label' => 'Nom',
                'label_attr' => [
                    'class' => 'form-label mt-2'
                ],
                'constraints' => [
                    new Assert\Length([
                        'min' => 2,
                        'max' => 100
                    ]),
                    new Assert\NotBlank([
                        'message' => 'Le nom est obligatoire'
                    ])
                ]
            ])
            ->add(
                'adresse',
                TextareaType::class,
                [
                    'attr' => [
                        'placeholder' => 'Entrez une adresse',
                        'class' => 'form-control',
                    ],
                    'label' => 'Adresse',
                    'label_attr' => [
                        'class' => 'form-label mt-2'
                    ],
                    'constraints' => [
                        new Assert\NotBlank([
                            'message' => 'L\'adresse est obligatoire'
                        ])
                    ]
                ]
            )
            ->add('conseil', ChoiceType::class, [
                'label' => 'Membre du conseil',
                'label_attr' => [
                    'class' => 'form-label mt-2'
                ],
                'attr' => [
                    'class' => 'form-control mt-2'
                ],
                'choices' => [
                    'Oui' => true,
                    'Non' => false,
                ],
                'expanded' => true,
                'multiple' => false,
                'required' => true,
            ])
            
            ->add('submit', SubmitType::class, [
                'label' => 'Modifier',
                'attr' => [
                    'class' => 'btn btn-primary btn-block mt-4'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
