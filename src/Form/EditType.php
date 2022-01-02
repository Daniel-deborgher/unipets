<?php

namespace App\Form;

use App\Entity\EditUpdate;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class EditType extends AbstractType
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EditUpdate::class,
        ]);
    }
}
