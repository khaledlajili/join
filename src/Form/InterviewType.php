<?php

namespace App\Form;

use App\Entity\Interview;
use App\Entity\RecruitmentSession;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Range;

class InterviewType extends AbstractType
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $currentRecruitmentSession = $this->em->getRepository(RecruitmentSession::class)->findOneBy(['current' => true]);

        $builder
            ->add('place')
            ->add('start',null,
                [
                    'widget' => 'single_text',
                    'constraints' => [
                        new Range([
                            'min' => $currentRecruitmentSession->getInterviewsStart()
                        ])
                    ]
                ])
            ->add('_end',null,
                [
                    'widget' => 'single_text',
                    'constraints' => [
                        new Range([
                            'max' => $currentRecruitmentSession->getInterviewsEnd()
                        ])
                    ]
                ])
            ->add('submit',SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Interview::class,
        ]);
    }
}
