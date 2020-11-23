<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', TextType::class, ['attr' => ['readonly' => true,
                //'class' => 'plaintext',
                'type' => 'text',
                'class' => 'form-control-plaintext']])
            ->add('birthDate', BirthdayType::class, ['attr' => ['disabled' => true]])
            ->add('sex', ChoiceType::class, ['choices' => [
                'male' => 'male',
                'female' => 'female'],
                'attr' => ['disabled' => true]])
            ->add('description', TextareaType::class, ['required'=> false,
                'attr' => ['readonly' => true]])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
