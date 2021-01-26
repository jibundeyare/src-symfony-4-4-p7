<?php

namespace App\Form;

use App\Entity\Project;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('users', EntityType::class, [
                // looks for choices from this entity
                'class' => User::class,

                // uses the User.username property as the visible option string
                'choice_label' => function($user) {
                    return "{$user->getFirstname()} {$user->getLastname()} ({$user->getId()})";
                },

                // used to render a select box, check boxes or radios
                'multiple' => true,
                'expanded' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
            'attr' => [
                'novalidate' => 'novalidate',
            ]
        ]);
    }
}
