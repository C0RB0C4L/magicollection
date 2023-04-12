<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LoginForm extends AbstractType
{
    public const AUTHENTICATOR_FIELD = "username"; // change this value if you use a different $userIdentifier in your security routine
    public const PASSWORD_FIELD = "password";
    public const CSRF_FIELD = "_csrf_token";
    public const CSRF_TOKEN_ID = "authenticate_login";

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(self::AUTHENTICATOR_FIELD, TextType::class, [
                'data' => $options['last_username'],
                'attr' => [
                    'autofocus' => true
                ]
            ])
            ->add(self::PASSWORD_FIELD, PasswordType::class, []);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_field_name' => self::CSRF_FIELD,
            'csrf_token_id'   => self::CSRF_TOKEN_ID, // arbitrary string used to generate the token (adds a hash)
            'last_username' => ""
        ]);
    }

    public function getBlockPrefix()
    {
        return ''; // removes the [form_name] prefix from the <input> name attribute
    }
}
