<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\{TextType,
    TextareaType,
    DateType,
    ChoiceType,
    FileType};
use Symfony\Component\Validator\Constraints\NotBlank;

class PreRegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach ($options['data']['fields'] as $field) {
            $params = ['label' => $field->getLabel()];

            if ($field->getRequired()) {
                $params['constraints'] = [
                    new NotBlank()
                ];
            } else
                $params['required'] = false;
            switch ($field->getType()) {
                case 'text':
                    $type = TextType::class;
                    break;
                case 'textarea':
                    $type = TextareaType::class;
                    break;
                case 'checkbox':
                    $type = ChoiceType::class;
                    $foptions = $field->getPreRegistrationFormFieldOptions();
                    $choices = [];
                    foreach ($foptions as $option) {
                        $choices[$option->getValue()] = $option->getId();
                    }
                    $params['choices'] = $choices;
                    $params['multiple'] = true;

                    break;
                case 'radio':
                    $type = ChoiceType::class;
                    $foptions = $field->getPreRegistrationFormFieldOptions();
                    $choices = [];
                    foreach ($foptions as $option) {
                        $choices[$option->getValue()] = $option->getId();
                    }
                    $params['choices'] = $choices;
                    $params['expanded'] = true;
                    $params['multiple'] = false;
                    break;
                case 'select':
                    $type = ChoiceType::class;
                    $foptions = $field->getPreRegistrationFormFieldOptions();
                    $choices = [];
                    foreach ($foptions as $option) {
                        $choices[$option->getValue()] = $option->getId();
                    }
                    $params['choices'] = $choices;
                    $params['expanded'] = false;
                    $params['multiple'] = false;
                    break;
                case 'date':
                    $type = DateType::class;
                    $params['widget'] = 'single_text';
                    break;
                case 'file':
                    $type = FileType::class;
                    break;

            }
            $builder->add($field->getId(), $type, $params);

        }
    }
}
