<?php

namespace App\Form;

use App\Entity\Interview;
use App\Entity\RecruitmentSession;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChangeInterviewType extends AbstractType
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $recruitmentSession = $this->em->getRepository(RecruitmentSession::class)->findOneBy(['current' => true]);
        $interviews=[];
        $allInterviews =$this->em->getRepository(Interview::class)->findBy(['recruitmentSession'=>$recruitmentSession,'candidate'=>Null]);
        foreach ($allInterviews as $interview){
            if(!$interview->getRecruiters()->isEmpty())
                $interviews[]=$interview;
        }
        $builder
            ->add('interview',null,
                [
                    'choices' => $interviews,
                    'required'=>true
                ])
            ->add('submit',SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
