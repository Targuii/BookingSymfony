<?php

namespace App\Form;

use App\Entity\User;
use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class AdminUserType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname',TextType::class,$this->getConfiguration("Nom",false))
            ->add('lastname',TextType::class,$this->getConfiguration("Nom",false))
            ->add('email',EmailType::class,$this->getConfiguration("E-mail",false))
            ->add('avatar',UrlType::class,$this->getConfiguration("avatar",false))
            ->add('introduction',TextType::class,$this->getConfiguration("Introduction",false))
            ->add('description',TextareaType::class,$this->getConfiguration("Description",false))
            ->add('userRoles')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
