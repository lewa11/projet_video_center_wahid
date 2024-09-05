<?php

namespace App\Form;

use App\Entity\Video;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;


class VideoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le titre ne peut pas être vide.',
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Le titre doit comporter au moins {{ limit }} lettres.',
                    ]),
                    new Callback(function ($value, ExecutionContextInterface $context) {
                        if (stripos($value, 'merde') !== false) {
                            $context->buildViolation('Le titre ne peut pas contenir des mots inappropriés.')
                                    ->addViolation();
                        }
                    }),
                ],
            ])
            ->add('videoLink', TextType::class, [
                'label' => 'Lien de la vidéo',
            ])
            ->add('description', TextType::class, [
                'label' => 'Description',
                'constraints' => [
                    new NotBlank([
                        'message' => 'La description ne peut pas être vide.',
                    ]),
                    new Length([
                        'min' => 20,
                        'minMessage' => 'La description doit comporter au moins {{ limit }} lettres!',
                    ]),
                    new Callback(function ($value, ExecutionContextInterface $context) {
                        if (stripos($value, 'wesh') !== false) {
                            $context->buildViolation('La description ne peut pas contenir des mots inappropriés.')
                                    ->addViolation();
                        }
                    }),
                ],
            ])
            ->add('premiumVideo', CheckboxType::class, [
                'label' => 'Vidéo Premium',
                'required' => false,
                ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Video::class,
        ]);
    }
}
