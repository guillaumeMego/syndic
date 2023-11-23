<?php

namespace App\Form;

use App\Entity\Problematiques;
use App\Entity\SuiviProblematique;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class EditProblematiquesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $excludeFields = $options['exclude_fields'] ?? [];

        if (!in_array('problematique', $excludeFields)) {
            $builder->add('problematique', TextType::class, [
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
            ]);
        }

        if (!in_array('description', $excludeFields)) {
            $builder->add('description', TextareaType::class, [
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
            ]);
        }

        if (!in_array('commentaire', $excludeFields)) {
            $builder->add('commentaire', TextareaType::class, [
                'attr' => [
                    'placeholder' => 'Entrez un commentaire',
                    'class' => 'form-control',
                    'minlength' => '2',
                    'maxlength' => '100'
                ],
                'label' => 'Commentaire',
                'label_attr' => [
                    'class' => 'form-label mt-2'
                ],
                'required' => false,
            ]);
        }

       


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

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Problematiques::class,
            'exclude_fields' => [], // Ajoutez cette ligne
        ]);
    }
}
