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
use Symfony\Component\Validator\Constraints\Image;

/**
 * Class RegisterType
 *
 * Class that represents form for registration of user
 *
 * @author Magdaléna Ondrušková <xondru16@stud.fit.vutbr.cz>
 * @package App\Form
 */
class RegisterType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', TextType::class,[
                'label' => 'Email *',
                'required' => true,
                'help' => 'For example: name@email.com',
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => true,
                'invalid_message' => 'Passwords do not match.',
                'first_options'  => ['label' => 'Password *'],
                'second_options' => ['label' => 'Confirm Password *',
                    'help' => 'Password should be at least 6 characters.'],
                ])
            ->add('firstName', TextType::class, [
                'required' => true,
                'label' => 'First Name *'
            ])
            ->add('lastName', TextType::class, [
                'required' => true,
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
                'label' => 'Select profile photo'])
            ->add('save', SubmitType::class, ['label' => 'Register',
                'attr' => array('class' => 'btn btn-primary float-right mt-3 mb-3')])
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
