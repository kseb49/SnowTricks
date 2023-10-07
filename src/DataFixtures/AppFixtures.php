<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Users;
use App\Entity\Groups;
use App\Service\Parameters;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    public function __construct(private UserPasswordHasherInterface $passwordHasher, private Parameters $parameters){}

    /**
     * Create fixtures
     *
     * @param ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $groups = ["grabs", "rails", "spins", "butters"];
        $images = ["snowboarder-310459.png", "avatar-default.png", "avatar_default_1.png"];
        // Create 10 confirmed users.All have the same password.
        for ($users = 0; $users < 10; $users++) {
            $user = new Users();
            $user->setName($faker->userName())
            ->setEmail($faker->safeEmail())
            ->setPassword($this->passwordHasher->hashPassword($user, '123456'))
            ->setPhoto($faker->randomElement($images))
            ->setRoles(['ROLE_USER'])
            ->setConfirmationDate($faker->dateTime())
            ->setSendLink(null);
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
