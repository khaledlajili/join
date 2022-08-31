<?php

namespace App\Form;

use App\Entity\Department;
use App\Entity\RecruitmentSession;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Expression;


class RecruitmentSessionType extends AbstractType
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $recruitmentSession = $this->em->getRepository(RecruitmentSession::class)->findOneBy(['current' => true]);
        $departmentsNbre = $this->em->getRepository(Department::class)->count([]);
        $now = new \DateTime();

        $builder
            ->add('name');
        if ($recruitmentSession && $recruitmentSession->getStart()) {
            if ($recruitmentSession->getRegistrationEnd() > $now) {
                $builder->add('registrationEnd', null, [
                    'label' => 'Candidates\' registration deadline',
                    'widget' => 'single_text',
                ]);
            }
            if ($recruitmentSession->getPreRegistrationSelectionEnd() > $now) {
                $builder->add('preRegistrationSelectionEnd', null, [
                    'label' => 'Pre-registration selection deadline',
                    'widget' => 'single_text',
                ]);
            }

            if ($recruitmentSession->getCollectiveInterviewsEnd() > $now) {
                $builder->add('collectiveInterviewsEnd', null, [
                    'widget' => 'single_text',
                ]);
            }
            if ($recruitmentSession->getCollectiveInterviewsSelectionEnd() > $now) {
                $builder->add('collectiveInterviewsSelectionEnd', null, [
                    'label' => 'Collective interviews selection deadline',
                    'widget' => 'single_text',
                ]);
            }
            if ($recruitmentSession->getTechnicalTestEnd() > $now) {
                $builder->add('technicalTestEnd', null, [
                    'label' => 'Technical test submission deadline',
                    'widget' => 'single_text',
                ]);
            }
            if ($recruitmentSession->getTechnicalTestSelectionEnd() > $now) {
                $builder->add('technicalTestSelectionEnd', null, [
                    'label' => 'Technical test selection deadline',
                    'widget' => 'single_text',
                ]);
            }
            if ($recruitmentSession->getInterviewsScheduleEnd() > $now) {
                $builder->add('interviewsScheduleEnd', null, [
                    'label' => 'Interviews schedule creation deadline',
                    'widget' => 'single_text',
                ]);
            }
            if ($recruitmentSession->getRecruitersAvailabilityEnd() > $now) {
                $builder->add('recruitersAvailabilityEnd', null, [
                    'label' => 'Recruiters availability declaration deadline',
                    'widget' => 'single_text',
                ]);
            }
            if ($recruitmentSession->getForBookingInterviewsScheduleEnd() > $now) {
                $builder->add('forBookingInterviewsScheduleEnd', null, [
                    'label' => 'Validate interviews schedule creation deadline (for booking)',
                    'widget' => 'single_text',
                ]);
            }
            if ($recruitmentSession->getValidateInterviewsScheduleEnd() > $now) {
                $builder->add('validateInterviewsScheduleEnd', null, [
                    'label' => 'Validate interviews schedule creation deadline',
                    'help' => 'If booking option is activated this date will be the booking deadline.',
                    'widget' => 'single_text',
                ]);
            }
            if ($recruitmentSession->getInterviewsStart() > $now) {
                $builder->add('interviewsStart', null, [
                    'label' => 'Interviews start date',
                    'widget' => 'single_text',
                ]);
            }
            if ($recruitmentSession->getInterviewsEnd() > $now) {
                $builder->add('interviewsEnd', null, [
                    'label' => 'Interviews end date',
                    'widget' => 'single_text',
                ]);
            }
            if ($recruitmentSession->getInterviewsSelectionEnd() > $now) {
                $builder->add('interviewsSelectionEnd', null, [
                    'label' => 'Interviews selection deadline',
                    'widget' => 'single_text',
                ]);
            }
            if ($recruitmentSession->getTrialPeriodSelectionEnd() > $now) {
                $builder->add('trialPeriodSelectionEnd', null, [
                    'label' => 'Trial period selection deadline',
                    'widget' => 'single_text',
                ]);
            }
        } else {
            $builder->add('registrationEnd', null, [
                'label' => 'Candidates\' registration deadline',
                'widget' => 'single_text',
            ]);
            $builder->add('preRegistrationSelectionEnd', null, [
                'label' => 'Pre-registration selection deadline',
                'widget' => 'single_text',
            ]);
            $builder->add('collectiveInterviewsEnd', null, [
                'widget' => 'single_text',
            ]);
            $builder->add('collectiveInterviewsSelectionEnd', null, [
                'label' => 'Collective interviews selection deadline',
                'widget' => 'single_text',
            ]);
            $builder->add('technicalTestEnd', null, [
                'label' => 'Technical test submission deadline',
                'widget' => 'single_text',
            ]);
            $builder->add('technicalTestSelectionEnd', null, [
                'label' => 'Technical test selection deadline',
                'widget' => 'single_text',
            ]);
            $builder->add('interviewsScheduleEnd', null, [
                'label' => 'Interviews schedule creation deadline',
                'widget' => 'single_text',
            ]);
            $builder->add('recruitersAvailabilityEnd', null, [
                'label' => 'Recruiters availability declaration deadline',
                'widget' => 'single_text',
            ]);
            $builder->add('forBookingInterviewsScheduleEnd', null, [
                'label' => 'Validate interviews schedule creation deadline (for booking)',
                'widget' => 'single_text',
            ]);
            $builder->add('validateInterviewsScheduleEnd', null, [
                'label' => 'Validate interviews schedule creation deadline',
                'help' => 'If booking option is activated this date will be the booking deadline.',
                'widget' => 'single_text',
            ]);
            $builder->add('interviewsStart', null, [
                'label' => 'Interviews start date',
                'widget' => 'single_text',
            ]);
            $builder->add('interviewsEnd', null, [
                'label' => 'Interviews end date',
                'widget' => 'single_text',
            ]);
            $builder->add('interviewsSelectionEnd', null, [
                'label' => 'Interviews selection deadline',
                'widget' => 'single_text',
            ]);
            $builder->add('trialPeriodSelectionEnd', null, [
                'label' => 'Trial period selection deadline',
                'widget' => 'single_text',
            ]);
            $builder
                ->add('collectiveInterview', null, [
                    'label' => false,
                    'label_attr' => [
                        'class' => 'checkbox-switch',
                    ]
                ])
                ->add('technicalTest', null, [
                    'label' => false,
                    'label_attr' => [
                        'class' => 'checkbox-switch',
                    ]
                ])
                ->add('bookingForInterview', null, [
                    'label' => false,
                    'label_attr' => [
                        'class' => 'checkbox-switch',
                    ]
                ])
                ->add('trialPeriod', null, [
                    'label' => false,
                    'label_attr' => [
                        'class' => 'checkbox-switch',
                    ]
                ]);
            $builder
                ->
                add('depChoiceMaxNbre', null, [
                    'label' => 'Departments choice limit',
                    'help' => 'The maximum number of departments that the candidate can choose.',
                    'constraints' => [
                        new Expression([
                            'expression' => 'value <= ' . $departmentsNbre,
                            'message' => 'this value should be lower than or equal to the number of departments.'
                        ])
                    ]
                ]);
        }
        $builder
            ->add('submit', SubmitType::class, [
                'label' => 'Save'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RecruitmentSession::class,
        ]);
    }
}
