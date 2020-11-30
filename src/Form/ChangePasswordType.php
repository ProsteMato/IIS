<?php

namespace App\Form;

use App\Entity\User;
use App\Form\Model\ChangePassword;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ChangePasswordType
 *
 *  Class that represents form for changing password
 *
 * @author Magdaléna Ondrušková <xondru16@stud.fit.vutbr.cz>
 * @package App\Form
 */
class ChangePasswordType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('oldPassword', PasswordType::class, ['required' => true,
                                                                   'label' => 'Old password'])
            ->add('newPassword', RepeatedType::class, ['type' => PasswordType::class,
                                                                   'invalid_message' => 'Passwords do not match.',
                                                                   'first_options'  => ['label' => 'New password'],
                                                                   'second_options' => ['label' => 'Confirm your new password']])
            ->add('save', SubmitType::class, ['label' => 'Change Password',
                'attr' => array('class' => 'btn btn-primary float-left mt-3 mb-3')])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ChangePassword::class,
        ]);
    }
}
