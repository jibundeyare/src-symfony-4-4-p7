<?php

namespace App\Form;

use App\Entity\Project;
use App\Entity\SchoolYear;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email')
            ->add('roles', ChoiceType::class, [
                'choices'  => [
                    'apprenant' => 'ROLE_STUDENT',
                    'formateur' => 'ROLE_TEACHER',
                    'client' => 'ROLE_CLIENT',
                ],
                'multiple' => true,
                'expanded' => true,
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
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => ['label' => 'Password'],
                'second_options' => ['label' => 'Repeat Password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
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
