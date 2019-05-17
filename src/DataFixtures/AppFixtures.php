<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use Faker\Factory;
use App\Entity\User;
use App\Entity\Image;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{

    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder){

        $this->encoder=$encoder;
    }

    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);
        
        $faker = Factory::create('fr_FR');
        $users=[];
        $genres=['male','female'];

        // UTILISATEURS

        for($i=1; $i<=10;$i++){
            $user =new User();
            $genre=$faker->randomElement($genres);
            $avatar ='https://randomuser.me/api/portraits/';
            $avatarId = $faker->numberBetween(1,99).'.jpg';
            $avatar .= ($genre == 'male' ? 'men/' : 'women/') . $avatarId;
            $hash = $this->encoder->encodePassword($user,'password');

            $description= "<p>".join("</p><p>",$faker->paragraphs(5))."</p>";
            $user->setDescription($description)
                 ->setFirstname($faker->firstname)
                 ->setLastname($faker->lastname)
                 ->setEmail($faker->email)
                 ->setIntroduction($faker->sentence())
                 ->setHash($hash)
                 ->setAvatar($avatar)
                 ;

            $manager->persist($user);
            $users[]=$user;

        }


        // ANNONCES

        for($i=1; $i<=20;$i++){
            $ad = new Ad();

            $title = $faker->sentence();
            $coverImage = $faker->imageUrl(700,300);
            $introduction = $faker->paragraph(2);
            $content = "<p>".join("</p><p>",$faker->paragraphs(5))."</p>";
            $user = $users[mt_rand(0,count($users)-1)];

            $ad->setTitle($title)
                ->setCoverImage($coverImage)
                ->setIntroduction($introduction)
                ->setContent($content)
                ->setPrice(mt_rand(30,200))
                ->setRooms(mt_rand(1,6))
                ->setAuthor($user)
                ;

            $manager->persist($ad);

            for($j=1;$j<=mt_rand(2,5);$j++){

             //on crée une nouvelle instance de l'entitée image

             $image = new Image();
             $image->setUrl($faker->imageUrl())
                   ->setCaption($faker->sentence())
                   ->setAd($ad)
                   ;

                // on sauvegarde
                $manager->persist($image);
            }
        }

        $manager->flush();
    }
}
