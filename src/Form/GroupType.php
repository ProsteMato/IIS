<?php

namespace App\Form;

use App\Entity\Group;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

/**
 * Class GroupType
 *
 * Class that represents form for creating group
 *
 * @package App\Form
 */
class GroupType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Name *',
                'empty_data' => ''
            ])
            ->add('description', TextareaType::class, [
                'required'=> false
            ])
            ->add('picture', FileType::class, [
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Image(['maxSize' => '4M'])
                ],
                'label' => 'Select group picture'
            ])
            ->add('save', SubmitType::class, [
                'label' => $options['label'],
                'attr' =>   [
                    'class' => 'btn btn-primary float-right mt-3 mb-3'
                ]
            ])
            ->add('visibility', CheckboxType::class, [
                'required'=> false,
                'label' => 'Anyone can see the group',
                'label_attr' => [
                    'class' => 'switch-custom'
                ]
            ])
            ->add('open', CheckboxType::class, [
                'required'=> false,
                'label' => 'Anyone can join the group',
                'label_attr' => [
                    'class' => 'switch-custom'
                ]
            ])

        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Group::class,
        ]);
    }
}