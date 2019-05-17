<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class RegistrationType extends ApplicationType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {


        $builder
            ->add('firstname',TextType::class,$this->getConfiguration("Nom","Votre Nom ..."))
            ->add('lastname',TextType::class,$this->getConfiguration("Prénom","Votre Prénom ..."))
            ->add('email',EmailType::class,$this->getConfiguration("E-mail","Un email valide ..."))
            ->add('avatar',UrlType::class,$this->getConfiguration("avatar","url de votre avatar"))
            ->add('hash',PasswordType::class,$this->getConfiguration("Mot de passe","Choisissez un mot de passe"))
            ->add('passwordConfirm',PasswordType::class,$this->getConfiguration("Confirmation Mot de Passe","Confirmez le mot de passe"))
            ->add('introduction',TextType::class,$this->getConfiguration("Introduction","Courte description ..."))
            ->add('description',TextareaType::class,$this->getConfiguration("Description","Description détaillée"))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
