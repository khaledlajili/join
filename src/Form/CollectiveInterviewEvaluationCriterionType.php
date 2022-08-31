<?php

namespace App\Form;

use App\Entity\CollectiveInterviewsEvaluationCriterion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Expression;

class CollectiveInterviewEvaluationCriterionType extends AbstractType
{
    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ): void {
        $builder
            ->add('criterion', null, [
                'constraints' => [
                    new Expression([
                        'expression' => 'value',
                        'message' => 'The criterion text should not be empty',
                    ]),
                ],
            ])
            ->add('Add', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CollectiveInterviewsEvaluationCriterion::class,
        ]);
    }
}
