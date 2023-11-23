<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

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
            ])
            ->add('email', TextType::class, [
                'attr' => [
                    'placeholder' => 'Entrez un email',
                    'class' => 'form-control',
                    'minlength' => '2',
                    'maxlength' => '100'
                ],
                'label' => 'Email',
                'label_attr' => [
                    'class' => 'form-label mt-2'
                ],
            ])
            ->add('telephone', TextType::class, [
                'attr' => [
                    'placeholder' => 'Entrez un numéro de téléphone',
                    'class' => 'form-control',
                    'minlength' => '2',
                    'maxlength' => '100'
                ],
                'label' => 'Téléphone',
                'label_attr' => [
                    'class' => 'form-label mt-2'
                ],
            ])
            ->add('batiment', ChoiceType::class, [
                'choices'  => [
                    'A' => 'A',
                    'B' => 'B',
                    'C' => 'C',
                ],
                'attr' => [
                    'placeholder' => 'Entrez un numéro de batiment',
                    'class' => 'form-select',
                    'minlength' => '2',
                    'maxlength' => '100'
                ],
                'label' => 'Batiment',
                'label_attr' => [
                    'class' => 'form-label mt-2'
                ],
            ])
            ->add('etage', IntegerType::class, [
                'attr' => [
                    'placeholder' => 'Entrez un numéro d\'étage',
                    'class' => 'form-control',
                    'min' => 0,
                    'max' => 99
                ],
                'label' => 'Etage',
                'label_attr' => [
                    'class' => 'form-label mt-2'
                ],
            ])
            ->add('numero_appartement', IntegerType::class, [
                'attr' => [
                    'placeholder' => 'Entrez un numéro d\'appartement',
                    'class' => 'form-control',
                    'min' => 0,
                    'max' => 999,
                ],
                'label' => 'Appartement',
                'label_attr' => [
                    'class' => 'form-label mt-2',
                ],
            ])
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'oui' => 'ROLE_CONSEIL',
                ],
                'expanded' => true,
                'multiple' => true,
                'label' => 'Membre du conseil ?',
                'label_attr' => [
                    'class' => 'form-label mt-2'
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Modifier le profil',
                'attr' => [
                    'class' => 'btn btn-outline-primary btn-sm btn-block mt-4'
                ]
            ]);

        if (!in_array('imageFile', $options['exclude_fields'])) {
            $builder->add('imageFile', VichImageType::class, [
                'required' => false,
                'label' => 'Photo de profil',
                'label_attr' => [
                    'class' => 'form-label mt-2'
                ],
                'download_uri' => false,
                'attr' => [
                    'class' => 'form-control'
                ],
                'allow_delete' => false,
                'image_uri' => false,
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'exclude_fields' => [],
        ]);
    }
}
