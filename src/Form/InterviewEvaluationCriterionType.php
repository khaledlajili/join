<?php

namespace App\Form;

use App\Entity\InterviewsEvaluationCriterion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InterviewEvaluationCriterionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('criterion',null,[
                'label' => false,
                'attr' => [
                    'placeholder' => 'criterion'
                ]]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => InterviewsEvaluationCriterion::class,
        ]);
    }
}
