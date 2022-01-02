<?php

namespace App\Form;

use App\Entity\EditPicture;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class EditPictureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('picture', FileType::class, [
            "label" => "Ajouter un avatar",
            
            'attr' => [
                'placeholder' => "(optionel)"
            ],
            "mapped" => false,
            "constraints" => [
                new File([
                    "mimeTypes" => ["image/jpeg", "image/png"],
                    "mimeTypesMessage" => "Formats autorisés : jpg, png",
                    "maxSize" => "4000k",
                    "maxSizeMessage" => "Le fichier ne doit pas dépasser 4mo"
                    ])
            ]
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EditPicture::class,
        ]);
    }
}
