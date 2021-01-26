<?php

namespace App\Form;

use App\Entity\Project;
use App\Entity\SchoolYear;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email')
            ->add('roles', ChoiceType::class, [
                'choices'  => [
                    'administrateur' => 'ROLE_ADMIN',
                    'utilisateur' => 'ROLE_USER',
                    'apprenant' => 'ROLE_STUDENT',
                    'formateur' => 'ROLE_TEACHER',
                    'client' => 'ROLE_CLIENT',
                ],
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => ['label' => 'Password'],
                'second_options' => ['label' => 'Repeat Password'],
            ])
            ->add('firstname')
            ->add('lastname')
            ->add('phone')
            ->add('projects', EntityType::class, [
                'class' => Project::class,
                'choice_label' => function($project) {
                    return "{$project->getName()} ({$project->getId()})";
                },
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('schoolYear', EntityType::class, [
                'class' => SchoolYear::class,
                'choice_label' => function($schoolYear) {
                    return "{$schoolYear->getName()} ({$schoolYear->getId()})";
                },
                'multiple' => false,
                'expanded' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
