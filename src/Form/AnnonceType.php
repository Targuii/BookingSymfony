<?php

namespace App\Form;

use App\Entity\Ad;
use App\Form\ImageType;
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

class AnnonceType extends AbstractType
{   
    /**
     * @param string $label
     * @param string $placeholder
     * @param array $options
     * @return array
     * 
     */
    
    private function getConfiguration($label,$placeholder,$options=[]){

        return array_merge([
            'label' => $label,
            'attr' => ['placeholder'=>$placeholder]
            ],
            $options);
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title',TextType::class,$this->getConfiguration('Titre','Inserer un titre'))
            ->add('slug',TextType::class,$this->getConfiguration('Alias (Facultatif)','Personalisez un alias pour générer l\'url',['required'=>false]))
            ->add('price',MoneyType::class,$this->getConfiguration('Prix','Prix par jour'))
            ->add('rooms',IntegerType::class,$this->getConfiguration('Nombre de chambre','Nombres de chambre'))
            ->add('coverImage',UrlType::class,$this->getConfiguration('Image de couverture','Inserez une image'))
            ->add('introduction',TextType::class,$this->getConfiguration('Résumé','Résumez votre bien'))
            ->add('content',TextareaType::class,$this->getConfiguration('Description détaillée','Décrivez vos services'))
            ->add('images',CollectionType::class,['entry_type'=>ImageType::class,'allow_add'=>true,'allow_delete'=>true])
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