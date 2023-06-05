<?php

namespace App\Form;

use App\Entity\User;
use App\Security\UserSecurityManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserRolesEditForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('roles', ChoiceType::class, [
                'mapped' => true,
                'required' => true,
                'choices' => array_values(UserSecurityManager::getRolesAll()),
                'choice_label' => function ($choice, $key, $value) {
                    return $choice;
                },
                'multiple' => true,
                'expanded' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
