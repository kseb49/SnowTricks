<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Users;
use App\Entity\Groups;
use App\Entity\Images;
use App\Entity\Videos;
use App\Entity\Figures;
use App\Entity\Messages;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;

class FiguresFixtures extends Fixture
{


    public function __construct(private SluggerInterface $slugger)
    {

    }


    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        $figures = [
                "grabs" => [
                    "mute",
                    "sad",
                    "indy",
                    "truck",
                    "driver",
                    "nose grab",
                    "style week",
                ],
                "rails" => [
                    "Boardslide",
                    "Lipslide",
                    "Bluntslide",
                ],
                "butters" => [
                    'Caballerial Butter',
                    'Half-Cab Butter',
                    'Pretzel Butter',
                ],
                "spins" => [
                    "1440",
                    '900',
                    '540',
                ]
            ];
        $photos = [
            "image_exemple_fixture.png",
            "image_exemple_fixture1.webp",
            "image_exemple_fixture2.jpg",
            "image_exemple_fixture3.jpg",
            "image_exemple_fixture4.jpeg",
            "image_exemple_fixture5.jpg",
            "image_exemple_fixture6.jpeg",
            ];
        $videos = [
            "https://www.youtube.com/embed/HDcW6k4M6t0?si=BS3DQmIyp7UTFtqF",
            "https://www.youtube.com/embed/H3G5kycAw6Q?si=p3TQcEErGzOOG9qj",
            "https://www.youtube.com/embed/NL4LiNT74Ic?si=sfdX0_6IajYN7o8o ?",
            "https://www.youtube.com/embed/Iofrv4rxJcY?si=ZAVtdpJtsYSjfmu1",
            "https://www.youtube.com/embed/Y4xeIZb178A?si=jCLMozB8V2rqVTyp"
            ];
        $ids = [];
        $users = $manager->getRepository(Users::class)->findAll();
        foreach ($users as $key => $value) {
            $ids[] = $value->getId();
        }

        // Create the tricks.
        foreach ($figures as $key => $value) {
            foreach ($value as $name) {
                $users = $manager->getRepository(Users::class)->findOneById($faker->randomElement($ids));
                $figure = new Figures();
                $figure->setName($name);
                $figure->setDescription($faker->text(1200));
                $figure->setSlug(strtolower($this->slugger->slug($name)));
                $figure->setCreationDate();
                $figure->setUsersId($users);
                $cat = $manager->getRepository(Groups::class)->findOneBy(['group_name' => $key]);
                $figure->setGroupsId($cat);
                // Create the images of the trick.
                for ($numbers = 0; $numbers < $faker->numberBetween(1, 5); $numbers++) {
                    $picture = new Images;
                    $picture->setImageName($faker->randomElement($photos));
                    $figure->addImage($picture);
                }

                // Create the videos of the trick.
                for ($numbers = 0; $numbers < $faker->numberBetween(1, 4); $numbers++) {
                    $embed = new Videos();
                    $embed->setSrc($videos[$numbers]);
                    $figure->addVideos($embed);
                }

                // Create the comments for the tricks.
                for ($comment = 0; $comment < $faker->numberBetween(10, 45); $comment++) {
                    $users = $manager->getRepository(Users::class)->findOneById($faker->randomElement($ids));
                    $message = new Messages();
                    $message->setContent($faker->sentence($faker->numberBetween(1,10)));
                    $message->setMessageDate();
                    $message->setUsers($users);
                    $figure->addMessage($message);
                }
                $manager->persist($figure);
            }
        }
        $manager->flush();

    }


}
