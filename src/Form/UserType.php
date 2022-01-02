<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserType extends AbstractType
{

    private function getConfiguration ($label, $placeholder, $options = []){
        return array_merge([
            'label' => $label,
            'attr' => [
                'placeholder' => $placeholder
            ]
            ], $options);
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, $this->getConfiguration("Prénom", "Votre prénom ..."))
            ->add('lastName', TextType::class, $this->getConfiguration("Nom", "Votre nom ..."))
            ->add('email', EmailType::class, $this->getConfiguration("Email", "Votre email ..."))
            ->add('picture', FileType::class, [
                "label" => "Ajouter un avatar",
                'attr' => [
                    'placeholder' => "(optionel)"
                ],
                'required' => false,
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
            
            ->add('hash', PasswordType::class, $this->getConfiguration("Mot de passe", "Votre mot de passe ..."))
            ->add('confirm', PasswordType::class, $this->getConfiguration("Confirmation du mot de passe", "Confirmez mot de passe ..."))
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
