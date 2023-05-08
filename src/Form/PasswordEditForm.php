<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class PasswordEditForm extends AbstractType
{
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
                            'min' => RegistrationForm::PASSWORD_MIN,
                            'max' => RegistrationForm::PASSWORD_MAX,
                        ]),
                        new Regex(['pattern' => RegistrationForm::PASSWORD_REGEX]),
                    ],
                ],
                'second_options' => [
                    'attr' => ['autocomplete' => 'new_password'],
                    'constraints' => [
                        new NotBlank(),
                        new Length([
                            'min' => RegistrationForm::PASSWORD_MIN,
                            'max' => RegistrationForm::PASSWORD_MAX,
                        ]),
                        new Regex(['pattern' => RegistrationForm::PASSWORD_REGEX]),
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
