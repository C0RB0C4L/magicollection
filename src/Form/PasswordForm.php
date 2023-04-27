<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class PasswordForm extends AbstractType
{
    private const MIN_LENGTH = 8;
    private const MAX_LENGTH = 36;
    /* public const REGEX_PASSWORD = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z\d])[A-Za-z\d\S]{4,}$/"; */
    public const REGEX_PASSWORD = "/[a-z]/";

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('currentPassword', PasswordType::class, [
                "constraints" => [
                    new UserPassword()
                ],
                'attr' => ['autocomplete' => 'old_password'],
                'mapped' => false,
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    'attr' => ['autocomplete' => 'new_password'],
                    'constraints' => [
                        new NotBlank(),
                        new Length([
                            'min' => self::MIN_LENGTH,
                            'max' => self::MAX_LENGTH,
                        ]),
                        new Regex(['pattern' => self::REGEX_PASSWORD]),
                    ],
                ],
                'second_options' => [
                    'attr' => ['autocomplete' => 'new_password'],
                    'constraints' => [
                        new NotBlank(),
                        new Length([
                            'min' => self::MIN_LENGTH,
                            'max' => self::MAX_LENGTH,
                        ]),
                        new Regex(['pattern' => self::REGEX_PASSWORD]),
                    ],
                ],
                'mapped' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
