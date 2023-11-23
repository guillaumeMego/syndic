<?php

namespace App\Form;

use App\Entity\Problematiques;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ProblematiquesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('problematique', TextType::class, [
                'attr' => [
                    'placeholder' => 'Entrez une problématique',
                    'class' => 'form-control',
                    'minlength' => '2',
                    'maxlength' => '100'
                ],
                'label' => 'Problématique',
                'label_attr' => [
                    'class' => 'form-label mt-2'
                ],
            ])
            ->add('description', TextareaType::class, [
                'attr' => [
                    'placeholder' => 'Entrez une description',
                    'class' => 'form-control',
                    'minlength' => '2',
                    'maxlength' => '100'
                ],
                'label' => 'Description',
                'label_attr' => [
                    'class' => 'form-label mt-2'
                ],
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary mt-3'
                ],
                'label' => 'Ajouter',
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
            'data_class' => Problematiques::class,
            'exclude_fields' => [],
        ]);
    }
}
