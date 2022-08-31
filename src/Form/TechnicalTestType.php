<?php

namespace App\Form;

use App\Entity\TechnicalTest;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class TechnicalTestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pdf', FileType::class, [
                'mapped' => false,
                'label' => false,
                'constraints' => [
                    new File([
                        'mimeTypes' => 'application/pdf',
                        'mimeTypesMessage' => 'please upload a pdf file.'
                    ])
                ]
            ])
            ->add('department', HiddenType::class, [
                'mapped' => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Submit'
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TechnicalTest::class,
        ]);
    }
}
