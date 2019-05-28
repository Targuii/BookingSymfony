<?php

namespace App\Form;

use App\Entity\Ad;
use App\Entity\Options;
use App\Form\ImageType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class AnnonceType extends ApplicationType
{   

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title',TextType::class,$this->getConfiguration('Titre','Inserer un titre'))
            ->add('slug',TextType::class,$this->getConfiguration('Alias (Facultatif)','Personalisez un alias pour générer l\'url',['required'=>false]))
            ->add('price',MoneyType::class,$this->getConfiguration('Prix','Prix par jour'))
            ->add('rooms',IntegerType::class,$this->getConfiguration('Nombre de chambre','Nombres de chambre'))
            ->add('options',EntityType::class,['class' => Options::class,'choice_label' => 'name','multiple'=>true])
            ->add('imageFile',FileType::class,$this->getConfiguration('Image de couverture','Inserez une image',['data_class' => null,'required'=>false]))
            ->add('introduction',TextType::class,$this->getConfiguration('Résumé','Résumez votre bien'))
            ->add('content',TextareaType::class,$this->getConfiguration('Description détaillée','Décrivez vos services'))
            ->add('images',CollectionType::class,['entry_type'=>ImageType::class,'allow_add'=>true,'allow_delete'=>true,'data_class' => null])
            // ->add('save',SubmitType::class,['label'=>'Enregistrez votre annonce','attr'=>['class'=>'btn btn-info']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ad::class,
        ]);
    }
}
