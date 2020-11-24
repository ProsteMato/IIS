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

class GroupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('visibility', CheckboxType::class, [
                'required'=> false,
                'label' => 'Anyone can see the group',
                'label_attr' => [
                                    'class' => 'switch-custom'
                                ]
            ])
            ->add('description', TextareaType::class, [
                'required'=> false
            ])
            ->add('picture', FileType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Select group picture'
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Register',
                'attr' =>   [
                            'class' => 'btn btn-primary float-right mt-3 mb-3'
                            ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Group::class,
        ]);
    }
}
