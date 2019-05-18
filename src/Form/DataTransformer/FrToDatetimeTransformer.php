<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;



class FrToDatetimeTransformer implements DataTransformerInterface{

    // transforme les données d'origine pour les affcher dans un formulaire
    public function transform($date){

        if ($date === null){
            return '';
        }
        // on retourne une date en Fr
        return $date->format('d/m/Y');
    }


    // c'est l'inverse. Elle prend les données qui arrive du formulaire et les remet dans le format qu'on attend
    public function reversetransform($datefr){

        // date en fr 18/05/2019

        if($datefr === null){
            // exception
            throw new TransformationFailedException("fournir une date");
        
        }
        $date = \Datetime::createFromFormat('d/m/Y',$datefr);

        if($date === false){
            //exception
            throw new TransformationFailedException("le format de la date n'est pas correct");
        }
        return $date;
    }

}