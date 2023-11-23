<?php
namespace App\Form;

use App\Form\EditProblematiquesType;
use App\Form\SuiviProblematiqueType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class CombinedFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('problematique', EditProblematiquesType::class)
            ->add('suiviProblematique', SuiviProblematiqueType::class)
            ->add('submit', SubmitType::class, [
                    'attr' => [
                        'class' => 'btn btn-primary mt-3'
                    ],
                    'label' => 'Modifier la problematique',
                ]);
            
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([])
        // mettre csrf_protection sur false pour que le formulaire fonctionne
        ->setDefaults(['csrf_protection' => false]);

    }
}