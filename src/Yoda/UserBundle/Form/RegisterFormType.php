<?php
/**
 * Created by PhpStorm.
 * User: ashleyberthon
 * Date: 10/02/2017
 * Time: 17:21
 */

namespace Yoda\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegisterFormType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('username', TextType::class)
            ->add('email', EmailType::class, array(
                'required' => false,
                'label'    => 'Email Address',
                'attr'     => array('class' => 'C-3PO'),
            ))
            ->add('plainPassword', RepeatedType::class, array(
                'type' =>'password'
            ));
        parent::buildForm( $builder,$options );
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Yoda\UserBundle\Entity\User'
        ));
    }
    public function getBlockPrefix()
    {
        return 'user_register';
    }
}