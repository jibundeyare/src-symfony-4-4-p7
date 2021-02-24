<?php

namespace App\Controller\Admin;

use App\Entity\SchoolYear;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class SchoolYearCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SchoolYear::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('name'),
            DateField::new('dateStart')
                ->setFormTypeOptions([
                    'widget' => 'single_text',
                    // 'format' => 'dd/MM/yyyy',
                    // 'html5' => false,
                ]),
            DateField::new('dateEnd')
                ->setFormTypeOptions([
                    'widget' => 'single_text',
                    // 'format' => 'dd/MM/yyyy',
                    // 'html5' => false,
                ]),
            AssociationField::new('users')
                ->setFormType(EntityType::class)
                ->setFormTypeOptions([
                    'choice_label' => function($user) {
                        return "{$user->getFirstname()} {$user->getLastname()} ({$user->getId()})";
                    }
                ]),
        ];
    }
}
