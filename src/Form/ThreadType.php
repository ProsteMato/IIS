<?php

namespace App\Form;

use App\Entity\Thread;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ThreadType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Title *',
                'required' => true
            ])
            ->add('description', TextareaType::class, [
                'required' => false
            ])
            ->add('create', SubmitType::class, [
                'label' => 'Create thread',
                'attr' =>   [
                    'class' => 'btn btn-primary float-right mt-3 mb-3'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Thread::class,
        ]);
    }
}
