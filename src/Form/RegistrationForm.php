<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class RegistrationForm extends AbstractType
{
    public const USERNAME_MIN = 4;
    public const USERNAME_MAX = 36;
    public const USERNAME_REGEX = "/[a-z]/";

    public const PASSWORD_MIN = 4;
    public const PASSWORD_MAX = 36;
    //public const PASSWORD_REGEX = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z\d])[A-Za-z\d\S]{4,}$/";
    public const PASSWORD_REGEX = "/[a-z]/";

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Regex(['pattern' => self::USERNAME_REGEX]),
                    new Length([
                        'min' => self::USERNAME_MIN,
                        'max' => self::USERNAME_MAX,
                    ])
                ]
            ])
            ->add('email', RepeatedType::class, [
                'type' => EmailType::class,
                'first_options' => [
                    'attr' => ['autocomplete' => 'email'],
                    'constraints' => [
                        new NotBlank(),
                    ],
                ],
                'second_options' => [
                    'attr' => ['autocomplete' => 'email'],
                    'constraints' => [
                        new NotBlank(),
                    ],
                ],
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    'attr' => ['autocomplete' => 'password'],
                    'constraints' => [
                        new NotBlank(),
                        new Length([
                            'min' => self::PASSWORD_MIN,
                            'max' => self::PASSWORD_MAX,
                        ]),
                        new Regex(['pattern' => self::PASSWORD_REGEX]),
                    ],
                ],
                'second_options' => [
                    'attr' => ['autocomplete' => 'password'],
                    'constraints' => [
                        new NotBlank(),
                        new Length([
                            'min' => self::PASSWORD_MIN,
                            'max' => self::PASSWORD_MAX,
                        ]),
                        new Regex(['pattern' => self::PASSWORD_REGEX]),
                    ],
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'constraints' => [
                new UniqueEntity([
                    'entityClass' => User::class,
                    'fields' => 'email',
                ]),
                new UniqueEntity([
                    'entityClass' => User::class,
                    'fields' => 'username',
                ]),
            ],
        ]);
    }
}
