<?php

namespace App\Form;

use App\Entity\SuiviProblematique;
use App\Enum\EtatProblematiqueEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class SuiviProblematiqueType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
            $builder->add('etat', ChoiceType::class, [
                'choices' => [
                    'En attente de validation' => 'En attente de validation',
                    'Non résolu' => 'Non résolu',
                    'En cours' => 'En cours',
                    'Résolu' => 'Résolu',
                    
                ],
                'label' => 'Etat',
                'label_attr' => [
                    'class' => 'form-label mt-2'
                ],
                'attr' => [
                    'class' => 'form-control'
                ],
            ])
            ->add('submit', SubmitType::class, [
                    'attr' => [
                        'class' => 'btn btn-primary mt-3'
                    ],
                    'label' => 'Modifier',
                ]);
            
            
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SuiviProblematique::class,
        ]);
    }
}
