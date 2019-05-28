<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ContactType extends ApplicationType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {


        $builder
            ->add('lastname',TextType::class,$this->getConfiguration("Lastname","Votre nom ..."))
            ->add('firstname',TextType::class,$this->getConfiguration("Firstname","Votre prénom ..."))
            ->add('phone',TextType::class,$this->getConfiguration('Phone','Votre téléphone ...'))
            ->add('email',EmailType::class,$this->getConfiguration("E-mail","Un email valide ..."))
            ->add('message',TextareaType::class,$this->getConfiguration('Message','Votre message ...'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Contact::class
        ]);
    }
}
