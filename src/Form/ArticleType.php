<?php

namespace App\Form;

use App\Entity\Sujet;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ArticleType extends AbstractType
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
            ->add('titre', TextType::class, $this->getConfiguration("Titre", "Titre de votre sujet ..."))
            ->add('contenu', TextareaType::class, $this->getConfiguration("Contenu", "Partagez-nous votre problème ..."))         
            ->add('image', FileType::class, [
                "mapped" => false,
                'required' => false,
                "constraints" => [
                    new File([
                        "mimeTypes" => ["image/jpeg", "image/png"],
                        "mimeTypesMessage" => "Formats autorisés : jpg, png",
                        "maxSize" => "2048k",
                        "maxSizeMessage" => "le fichier ne doit pas dépasser 2mo"
                        ])
                ]
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'titre',
                'label' => 'Choisissez votre categorie d\'animaux'
            ])
            ->add('averti', CheckboxType::class, [
                'label' => 'Être averti par e-mail des réponses',
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sujet::class,
        ]);
    }
}
