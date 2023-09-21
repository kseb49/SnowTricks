<?php

// namespace App\DataFixtures;

// use Faker\Factory;
// use App\Entity\Users;
// use App\Entity\Figures;
// use App\Entity\Messages;
// use Doctrine\ORM\Mapping\Entity;
// use Doctrine\Persistence\ObjectManager;
// use Doctrine\Bundle\FixturesBundle\Fixture;

// class MessagesFixtures extends Fixture
// {

    // public function __construct(public ObjectManager $manager){}


    // /**
    //  * Create 100 comments
    //  *
    //  * @return void
    //  */
    // public function load(): void
    // {
    //     $faker = Factory::create('fr_FR');
    //     $users = $this->manager->getRepository(Users::class)->findAll();
    //     $figures = $this->manager->getRepository(Figures::class)->findAll();
    //     for ($com=0; $com < 100 ; $com++) { 
    //         $comment = new Messages();
    //         $comment->setContent()->setMessageDate()->setUsers($this->randomOne($users, Users::class))->setFigures($this->randomOne($figures, Figures::class));
    //         $this->manager->persist($comment);
    //     }
    //     $this->manager->flush();
    // }


    // /**
    //  * Get a single object randomly
    //  *
    //  * @param Entity $entity
    //  * @return Users|Figures
    //  */
    // public function randomOne(Entity $entity, string $class) :Users|Figures
    // {
    //     $results = [];
    //     foreach ($entity as $value) {
    //         $results[] = $value->getId();
    //     }
    //     $object = $this->manager->getRepository($class)->find($results[array_rand($results)]);
    //     return $object;

    // }


// }
