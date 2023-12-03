<?php

namespace App\Form;

use App\Entity\Recherche;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class RechercheType extends AbstractType
{
    /**
     * Builds the form.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array $options The options for the form
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('q', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Entrer votre recherche',
                    'class' => 'form-control'
                ],
                'required' => false
            ])
        ;
    }

    /**
     * Configure les options du formulaire de recherche.
     *
     * @param OptionsResolver $resolver Le résolveur d'options.
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recherche::class,
            'method' => 'GET',
            'csrf_protection' => false
        ]);
    }

    /**
     * Retourne le préfixe du formulaire.
     *
     * @return string The block prefix.
     */
    public function getBlockPrefix(): string
    {
        return '';
    }
}
