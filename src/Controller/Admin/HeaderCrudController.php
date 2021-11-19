<?php

namespace App\Controller\Admin;

use App\Entity\Header;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class HeaderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Header::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('title', 'Titre du carousel'),
            TextareaField::new('content', 'Contenu du carousel'),
            TextField::new('btnTitle', 'Titre du bouton'),
            TextField::new('btnUrl', 'Url du bouton'),
            ImageField::new('image')->setBasePath('/uploads/images')
                ->setUploadDir('public/uploads/images')
        ];
    }
}
