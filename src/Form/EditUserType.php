<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', TextType::class)
            ->add('firstName', TextType::class)
            ->add('lastName', TextType::class)
            ->add('birthDate', BirthdayType::class)
            ->add('sex', ChoiceType::class, ['choices' => [
                'male' => 'male',
                'female' => 'female']])
            ->add('visibility', CheckboxType::class, ['required'=> false,
                                                                  'label' => 'Visible',
                                                                   'label_attr' => ['class' => 'switch-custom']])
            ->add('description', TextareaType::class, ['required'=> false])
            ->add('attachment', FileType::class, [ 'mapped' => false,
                'required' => false,
                'label' => 'Select new profile photo'])
            ->add('save', SubmitType::class, ['label' => 'Update profile',
                'attr' => array('class' => 'btn btn-primary float-left mb-5 mt-3')])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}