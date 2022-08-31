<?php

namespace App\Form;

use App\Entity\Interview;
use App\Entity\InterviewsAvailability;
use App\Entity\RecruitmentSession;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ValidateInterviewType extends AbstractType
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $currentRecruitmentSession = $this->em->getRepository(RecruitmentSession::class)->findOneBy(['current' => true]);
        $recruiters=$this->em->getRepository(User::class)->findByRoleAndSession('ROLE_RECRUITER',$currentRecruitmentSession);
        $candidats=$this->em->getRepository(User::class)->findByRoleAndSession('ROLE_CANDIDATE',$currentRecruitmentSession);
        $candidatsWithoutInterview=[];
        $recruitersAvailible=[];
        foreach ($candidats as $candidat){
            if (($candidat->getInterview() === null && !$candidat->isRefused()) || ($options["data"]->getCandidate()&&$candidat->getId() == $options["data"]->getCandidate()->getId())){
                $candidatsWithoutInterview[]=$candidat;
            }
        }
        foreach($recruiters as $recruiter){
            $availibilty = $this->em->getRepository(InterviewsAvailability::class)->findOneBy(["recruiter"=>$recruiter,"interview"=>$options["data"]]);
            if(($availibilty && $availibilty->getAvailable()===true) || !$availibilty ){
                $recruitersAvailible[]=$recruiter;
            }
        }

        $builder
            ->add('recruiters',null,
            [
                'choices' => $recruitersAvailible,
                'choice_label' => 'f_name'
            ]);
            if(!$options["data"]->getRecruitmentSession()->getBookingForInterview()){
                $builder->add('candidate',null,
                    [
                        'choices' => $candidatsWithoutInterview,
                        'choice_label' => 'f_name'
                    ]);
            }
            $builder->add('submit',SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Interview::class,
        ]);
    }
}
