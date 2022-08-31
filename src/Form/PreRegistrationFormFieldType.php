<?php

namespace App\Form;

use App\Entity\PreRegistrationFormField;
use App\Entity\RecruitmentSession;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PreRegistrationFormFieldType extends AbstractType
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $recruitmentSession = $this->em->getRepository(RecruitmentSession::class)->findOneBy(['current' => true]);

        $builder
            ->add('label', null, [
                'label' => 'Question :'
            ])
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Short answer' => 'text',
                    'Long answer' => 'textarea',
                    'Select menu' => 'select',
                    'Multiple choice' => 'checkbox',
                    'Single choice' => 'radio',
                    'File' => 'file',
                    'Date' => 'date',
                ],
                'label' => false,
            ])
            ->add('required');
        if ($recruitmentSession && $recruitmentSession->getDepChoiceMaxNbre()) {
            $builder
                ->add('departments', null, [
                    'choice_label' => 'name'
                ]);
        }
        $builder
            ->add('preRegistrationFormFieldOptions', CollectionType::class, [
                'label' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'entry_type' => PreRegistrationFormFieldOptionType::class,
                'entry_options' => [
                    'label' => false,
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PreRegistrationFormField::class,
        ]);
    }
}
