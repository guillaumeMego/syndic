<?php 

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class UserPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder    
        ->add('plainPassword', RepeatedType::class,
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
                ],
            ],
            'second_options' => [
                'attr' => [
                    'placeholder' => 'Entrez un mot de passe',
                    'class' => 'form-control',
                ],
                'label' => 'Confirmation du mot de passe',
                'label_attr' => [
                    'class' => 'form-label mt-2'
                ],
            ]
        ],
    )
        ->add('newPassword', PasswordType::class, [
            'attr' => [
                'placeholder' => 'Entrez un mot de passe',
                'class' => 'form-control',
           /*      'minlength' => '12', */
                'maxlength' => '255'
            ],
            'label' => 'Nouveau mot de passe',
            'label_attr' => [
                'class' => 'form-label mt-2'
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
        ])
        ->add('submit', SubmitType::class, [
            'label' => 'Changer mot de passe',
            'attr' => [
                'class' => 'btn btn-primary btn-block mt-4'
            ]
        ])
    ;
    }
}