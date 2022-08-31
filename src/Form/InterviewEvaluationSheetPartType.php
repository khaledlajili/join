<?php

namespace App\Form;

use App\Entity\InterviewsEvaluationSheetPart;
use App\Entity\RecruitmentSession;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InterviewEvaluationSheetPartType extends AbstractType
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
            ->add('name')
            ->add('coefficient');
        if ($recruitmentSession && $recruitmentSession->getDepChoiceMaxNbre()) {
            $builder
                ->add('departments', null, [
                    'choice_label' => 'name'
                ]);
        }
        $builder
            ->add('interviewsEvaluationCriteria', CollectionType::class, [
                'label' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'entry_type' => InterviewEvaluationCriterionType::class,
                'entry_options' => [
                    'label' => false,
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => InterviewsEvaluationSheetPart::class,
        ]);
    }
}
