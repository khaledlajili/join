<?php

namespace App\Form;

use App\Entity\Feedback;
use App\Entity\RecruitmentSession;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FeedbackType extends AbstractType
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $recruitmentSession = $this->em->getRepository(RecruitmentSession::class)->findOneBy(['current' => true]);
        $feedback=$options['data'];
        $user=$feedback->getUser();
        $today=new \DateTime();
        if(!$user->getResponses()->isEmpty() && $feedback->getPreRegistrationRating() == null){
            $builder
                ->add('preRegistrationRating',HiddenType::class,["required"=>false,"empty_data"=>0])
                ->add('preRegistrationRemark',TextareaType::class,["required"=>false]);
        }
        if($user->getCollectiveInterview() != null && $today > $user->getCollectiveInterview()->getEnd() && $feedback->getCollectiveInterviewsRating() == null){
            $builder
                ->add('collectiveInterviewsRating',HiddenType::class,["required"=>false,"empty_data"=>0])
                ->add('collectiveInterviewsRemark',TextareaType::class,["required"=>false]);
        }
        if (!$user->getTechnicalTestResults()->isEmpty() && $feedback->getTechnicalTestRating() == null) {
            $builder
                ->add('technicalTestRating', HiddenType::class, ["required" => false,"empty_data"=>0])
                ->add('technicalTestRemark', TextareaType::class, ["required" => false]);
        }
        if ($user->getInterview() != null && $today > $user->getInterview()->getEnd() && $feedback->getIndividualInterviewsRating() == null) {
            $builder
                ->add('individualInterviewsRating',HiddenType::class,["required"=>false,"empty_data"=>0])
                ->add('individualInterviewsRemark',TextareaType::class,["required"=>false]);
        }
        if ($user->getResult() != null && $user->getResult()->getTrialPeriod() === true && $today > $recruitmentSession->getTrialPeriodSelectionEnd() && $feedback->getTrialPeriodRating() == null) {
            $builder
                ->add('trialPeriodRating',HiddenType::class,["required"=>false,"empty_data"=>0])
                ->add('trialPeriodRemark',TextareaType::class,["required"=>false]);
        }
        $builder->add('submit',SubmitType::class);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Feedback::class
        
        ]);
    }
}
