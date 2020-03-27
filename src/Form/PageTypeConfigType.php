<?php

namespace App\Form;

use App\Entity\PageTypeConfig;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PageTypeConfigType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('pageTypeName')
            ->add('conditionValue', ChoiceType::class, [
                'required' => true,
                'choices' => [
                    '---' => '',
                    'Is' => 'is',
                    'Contains' => 'contains',
                ],
            ])
            ->add('conditionTerm')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PageTypeConfig::class,
        ]);
    }
}
