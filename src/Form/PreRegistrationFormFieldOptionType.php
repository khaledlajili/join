<?php

namespace App\Form;

use App\Entity\PreRegistrationFormFieldOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PreRegistrationFormFieldOptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('value',null,[
                'label' => false,
                'attr' => [
                    'placeholder' => 'option'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PreRegistrationFormFieldOption::class,
        ]);
    }
}
