<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email; // Add this line
use Symfony\Component\Form\Extension\Core\Type\EmailType;


class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('firstname', null, [
            'constraints' => [
                new NotBlank([
                    'message' => 'Le prénom est obligatoire.',
                ]),
                new Length([
                    'min' => 2,
                    'minMessage' => 'Le prénom doit comporter au moins {{ limit }} caractères.',
                ]),
            ],
        ])
        ->add('lastname', null, [
            'constraints' => [
                new NotBlank([
                    'message' => 'Le nom est obligatoire.',
                ]),
                new Length([
                    'min' => 2,
                    'minMessage' => 'Le nom doit comporter au moins {{ limit }} caractères.',
                ]),
            ],
        ])
        ->add('email', EmailType::class, [
            'constraints' => [
                new NotBlank([
                    'message' => 'L\'adresse email est obligatoire.',
                ]),
                new Length([
                    'min' => 5,
                    'minMessage' => 'L\'adresse email doit comporter au moins {{ limit }} caractères.',
                ]),
                new Email([
                    'message' => 'Veuillez entrer une adresse email valide.',
                ]),
            ],
        ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter nos conditions.',
                    ]),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un mot de passe',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit comporter au moins {{ limit }} caractères',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
