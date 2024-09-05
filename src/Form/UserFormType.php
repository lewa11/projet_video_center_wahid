<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;



class UserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
                ->add('firstname',TextType::class, [
                'label' => 'userForm.firstname'
                ])
                ->add('lastname',TextType::class, [
                'label' => 'userForm.lastname'
                ])         
                ->add('email',TextType::class, [
                    'label' => 'userForm.email'
                    ]) 
                    ->add('avatarFile', FileType::class, [
                        'label' => 'userForm.avatar',
                        'required' => false,
                        'mapped' => false, // Ne pas mapper directement à l'entité
                        'constraints' => [
                            new Assert\Image([
                                'maxSize' => '2M',
                                'mimeTypesMessage' => 'Please upload a valid image file.',
                            ])
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
