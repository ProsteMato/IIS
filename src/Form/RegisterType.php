<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', TextType::class)
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => true,
                'invalid_message' => 'Passwords do not match.',
                'first_options'  => ['label' => 'Password'],
                'second_options' => ['label' => 'Confirm Password'],
                'label_attr' => ['class' => 'required']])
            ->add('firstName', TextType::class)
            ->add('lastName', TextType::class)
            ->add('birthDate', BirthdayType::class)
            ->add('sex', ChoiceType::class, ['choices' => [
                'male' => 'male',
                'female' => 'female']])
            ->add('visibility', CheckboxType::class, ['required'=> false,
                                                                  'label' => 'Anyone can see my profile',
                                                                   'label_attr' => ['class' => 'switch-custom']])
            ->add('description', TextareaType::class, ['required'=> false])
            ->add('attachment', FileType::class, [ 'mapped' => false,
                'required' => false,
                'label' => 'Select profile photo'])
            ->add('save', SubmitType::class, ['label' => 'Register',
                'attr' => array('class' => 'btn btn-primary float-right mt-3 mb-3')])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
