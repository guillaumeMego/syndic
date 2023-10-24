<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class RegistrationType extends AbstractType
{
    /**
     * Formulaire d'inscription
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
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
            ->add('email', EmailType::class, [
                'attr' => [
                    'placeholder' => 'Entrez un mail',
                    'class' => 'form-control',
                    'minlength' => '2',
                    'maxlength' => '320'
                ],
                'label' => 'Mail',
                'label_attr' => [
                    'class' => 'form-label mt-2'
                ],
                'constraints' => [
                    new Assert\Email([
                        'message' => 'Le mail n\'est pas valide'
                    ]),
                    new Assert\Length([
                        'min' => 2,
                        'max' => 320,
                    ]),
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
            ->add('roles', ChoiceType::class, [
                'label' => 'Rôle',
                'choices' => [
                    'Propriétaire' => 'ROLE_PROPRIETAIRE',
                    'Locataire' => 'ROLE_LOCATAIRE',
                ],
                'attr' => [
                    'class' => 'form-select',
                ],
                'label_attr' => [
                    'class' => 'form-label mt-2'
                ],
                'multiple' => true,
            ])
            ->add(
                'plainPassword',
                RepeatedType::class,
                [
                    'type' => PasswordType::class,
                    'invalid_message' => 'Les mots de passe doivent être identiques',
                    'first_options' => [
                        'label' => 'Mot de passe',
                        'label_attr' => [
                            'class' => 'form-label mt-2'
                        ],
                        'attr' => [
                            'placeholder' => 'Entrez un mot de passe',
                            'class' => 'form-control',
                            'minlength' => '12',
                            'maxlength' => '255'
                        ],
                    ],
                    'second_options' => [
                        'attr' => [
                            'placeholder' => 'Entrez un mot de passe',
                            'class' => 'form-control',
                            'minlength' => '12',
                            'maxlength' => '255'
                        ],
                        'label' => 'Confirmation du mot de passe',
                        'label_attr' => [
                            'class' => 'form-label mt-2'
                        ],
                    ],
                    'constraints' => [
                        new Assert\Regex([
                            'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).{12,}$/',
                            'message' => 'Le mot de passe doit contenir au moins 12 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial'
                        ]),
                        new Assert\NotBlank([
                            'message' => 'Le mot de passe est obligatoire'
                        ])
                    ]
                ],
            )
            ->add('submit', SubmitType::class, [
                'label' => 'S\'inscrire',
                'attr' => [
                    'class' => 'btn btn-primary btn-block mt-4'
                ]
            ]);
    }

    /**
     * Configuration du formulaire
     *
     * @param OptionsResolver $resolver
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
