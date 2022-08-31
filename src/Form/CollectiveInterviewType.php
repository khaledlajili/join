<?php

namespace App\Form;

use App\Entity\CollectiveInterview;
use App\Entity\RecruitmentSession;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Range;

class CollectiveInterviewType extends AbstractType
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $currentRecruitmentSession = $this->em->getRepository(RecruitmentSession::class)->findOneBy(['current' => true]);
        $recruiters = $this->em->getRepository(User::class)->findByRoleAndSession('ROLE_RECRUITER', $currentRecruitmentSession);
        $candidats = $this->em->getRepository(User::class)->findByRoleAndSession('ROLE_CANDIDATE', $currentRecruitmentSession);
        $candidatsWithoutCollectiveInterview = [];

        foreach ($candidats as $candidat) {
            if ($candidat->getCollectiveInterview() === null && $candidat->isRefused() === false) {
                $candidatsWithoutCollectiveInterview[] = $candidat;
            }
        }
        $builder
            ->add('place')
            ->add('start', null,
                [
                    'widget' => 'single_text',
                    'constraints' => [
                        new Range([
                            'min' => $currentRecruitmentSession->getPreRegistrationSelectionEnd()
                        ])
                    ]
                ])
            ->add('end', null,
                [
                    'widget' => 'single_text',
                    'constraints' => [
                        new Range([
                            'max' => $currentRecruitmentSession->getCollectiveInterviewsEnd()
                        ])
                    ]
                ])
            ->add('recruiters', null,
                [
                    'choices' => $recruiters,
                    'choice_label' => 'f_name'
                ])
            ->add('users', null,
                [
                    'label' => 'Candidates',
                    'choices' => $candidatsWithoutCollectiveInterview,
                    'choice_label' => 'f_name'
                ])
            ->add('submit', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CollectiveInterview::class,
        ]);
    }
}
