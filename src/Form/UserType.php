<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username')
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'Super' => 'ROLE_SUPER',
                    'Admin' => 'ROLE_ADMIN',
                    'Redac' => 'ROLE_REDAC',
                    'User' => 'ROLE_USER',
                    // Add other roles as needed
                ],
                'multiple' => true, // Allows selecting multiple roles
                'expanded' => true, // Displays as checkboxes instead of a multi-select box
                'label' => 'Roles',
            ])
           // ->add('password')
            ->add('fullname')
           // ->add('uniqid')
            ->add('email')
            ->add('activate')
            ->add('img_loc')
            ->add('quote')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
