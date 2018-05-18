<?php

namespace App\DataFixtures;

use App\Entity\Message;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker;

class MessageFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('en_US');

        for ($i = 0; $i < 50; $i++) {
            $message = new Message();
            $message->setSender($this->getReference('user_' . $faker->numberBetween(0,4)));
            $message->setAddressee($this->getReference('user_' . $faker->numberBetween(0,4)));
            $message->setMessageText($faker->sentence);
            $message->setSeen(false);

            $manager->persist($message);
        }

        $manager->flush();
    }

    /**
     * @return array
     */
    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
