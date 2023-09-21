<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Users;
use App\Entity\Groups;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    public function __construct(private UserPasswordHasherInterface $passwordHasher){}

    /**
     * Create fixtures
     *
     * @param ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $groups = ["grabs, rails, spins, butters"];
        // Create 10 users.
        for ($users = 0; $users < 10; $users++) { 
            $user = new Users();
            $user->setName($faker->userName())
            ->setEmail($faker->safeEmail())
            ->setPassword($this->passwordHasher->hashPassword($user, $faker->password()))
            ->setPhoto()
            ->setConfirmationDate($faker->dateTime())
            ->setSendLink(null)
            ->setToken(null);
            $manager->persist($user);
        }
        // Create groups.
        foreach ($groups as $value) {
            $group = new Groups();
            $group->setGroupName($value);
            $manager->persist($group);
        }
        $manager->flush();
    }
}
