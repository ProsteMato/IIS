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
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

/**
 * Class EditUserType
 *
 * Class that represents form for editing user profile
 *
 * @author Magdaléna Ondrušková <xondru16@stud.fit.vutbr.cz>
 * @package App\Form
 */
class EditUserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', TextType::class, [
                'label' => 'Email *',
                'empty_data' => '',
                'required' => true,
            ])
            ->add('firstName', TextType::class, [
                'required' => true,
                'empty_data'=> '',
                'label' => 'First Name *'
            ])
            ->add('lastName', TextType::class, [
                'required' => true,
                'empty_data' => '',
                'label' => 'Last Name *'
            ])
            ->add('birthDate', BirthdayType::class, [
                'placeholder' => [
                    'year' => 'Year', 'month' => 'Month', 'day' => 'Day'],
                'by_reference' => true,
                'required' => false,
            ])
            ->add('sex', ChoiceType::class, ['choices' => [
                '' => NULL,
                'male' => 'male',
                'female' => 'female']])
            ->add('visibility', ChoiceType::class, [
                'label' => 'Select who can see your profile *',
                'choices' => [
                    'everyone' => 'everyone',
                    'only registered users' => 'registered',
                    'only members of same groups' => 'members',
                    'only me'=> 'noone'
                ]
            ])
            ->add('description', TextareaType::class, ['required'=> false])
            ->add('attachment', FileType::class, [ 'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Image(['maxSize' => '4M'])
                ],
                'label' => 'Select new profile photo'])
            ->add('save', SubmitType::class, ['label' => 'Update profile',
                'attr' => array('class' => 'btn btn-primary float-left mb-5 mt-3')])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
