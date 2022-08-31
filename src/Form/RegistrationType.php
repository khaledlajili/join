<?php

namespace App\Form;

use App\Entity\RecruitmentSession;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Expression;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class RegistrationType extends AbstractType
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $recruitmentSession = $this->em->getRepository(RecruitmentSession::class)->findOneBy(['current' => true]);
        $depChoiceMaxNbre = $recruitmentSession->getDepChoiceMaxNbre();

        $builder
            ->add('email', null, [
                'attr' => [
                    'placeholder' => 'Email'
                ],
                'label' => false,
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'required' => true,
                'label' => false,
                'first_options' => ['label' => false, 'attr' => ['placeholder' => 'Password']],
                'second_options' => ['label' => false, 'attr' => ['placeholder' => 'Repeat Password']],
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'your password must contain at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('fName', null, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'First name'
                ],
            ])
            ->add('lName', null, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Last name'
                ],
            ])
            ->add('phone', TelType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Phone number'
                ],
            ])
            ->add('adress', null, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Adresse'
                ],
            ])
            ->add('studyLevel', null,
                ['placeholder' => 'choose your study level',
                    'choice_label' => 'name',
                    'label' => false,
                    'constraints' => [
                        new NotBlank()
                    ]
                ]);
        if ($depChoiceMaxNbre > 0) {
            $builder
                ->add('departments', null,
                    [
                        'placeholder' => 'choose your departments',
                        'choice_label' => 'name',
                        'label' => false,
                        'constraints' => [
                            new Expression([
                                'expression' => '1<=value.count() && value.count() <= ' . $depChoiceMaxNbre,
                                'message' => 'You must choose between 1 and '. $depChoiceMaxNbre .' departments.'
                            ])
                        ]
                    ]);
        }
        $builder->add('birthday', null, [
            'widget' => 'single_text',
            'label' => 'Birthdate'
        ])
            ->add('img', FileType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Image',
                'constraints' => [
                    new Image()
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
