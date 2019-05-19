<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use Faker\Factory;
use App\Entity\Role;
use App\Entity\User;
use App\Entity\Image;
use App\Entity\Booking;
use App\Entity\Comment;
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
        // GESTION DES ROLES
        $adminRole = new Role();
        $adminRole->setTitle('ROLE_ADMIN');
        $manager->persist($adminRole);

        // CREATION D'UNE UTILISATEUR AVEC UN ROLE ADMIN
        $adminUser = new User();

        $adminUser->setFirstname('Guillaume')
                  ->setLastname('Fontaine')
                  ->setEmail('guru@chamanik.org')
                  ->setHash($this->encoder->encodePassword($adminUser,'Qmn1_qrki'))
                  ->setAvatar('https://secure.gravatar.com/avatar/5ea3c583b54f7180a0ff6c42fdc6f296')
                  ->setIntroduction($faker->sentence())
                  ->setDescription("<p>".join("</p><p>",$faker->paragraphs(5))."</p>")
                  ->addUserRole($adminRole)
                  ;
        $manager->persist($adminUser);

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
            // GESTION DES RESERVATIONS

            for($k=1;$k <= mt_rand(0,5);$k++){

                $booking = new Booking();
                $createdAt = $faker->dateTimeBetween('- 6months');
                $startDate = $faker->dateTimeBetween('-3 months');
                $duration = mt_rand(3,10);
                $endDate = (clone $startDate)->modify("+ $duration days");
                $amount = $ad->getPrice() * $duration;

                // trouver le booker
                $booker = $users[mt_rand(0,count($users)-1)];
                $comment = $faker->paragraph();

                // configuration de la réservation

                $booking->setBooker($booker)
                        ->setAd($ad)
                        ->setStartDate($startDate)
                        ->setEndDate($endDate)
                        ->setCreatedAt($createdAt)
                        ->setAmount($amount)
                        ->setComment($comment)
                        ;

                        $manager->persist($booking);

                // GESTION DES COMMENTAIRES

                $comment = new Comment();

                if(mt_rand(0,1)){

                    $comment->setContent($faker->paragraph())
                    ->setRating(mt_rand(1,5))
                    ->setAuthor($booker)
                    ->setAd($ad)
                    ;

                    $manager->persist($comment);
                    
                }
            }
        }
        $manager->flush();
    }
}
