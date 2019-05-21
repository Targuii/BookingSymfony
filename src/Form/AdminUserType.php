<?php

namespace App\Form;

use App\Entity\User;
use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;


class AdminUserType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lastname',TextType::class,$this->getConfiguration("Nom",false))
            ->add('firstname',TextType::class,$this->getConfiguration("Prénom",false))
            ->add('email',EmailType::class,$this->getConfiguration("E-mail",false))
            ->add('avatar',UrlType::class,$this->getConfiguration("avatar",false))
            ->add('introduction',TextType::class,$this->getConfiguration("Introduction",false))
            ->add('description',TextareaType::class,$this->getConfiguration("Description",false))
            // ->add('userRoles')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
