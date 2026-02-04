<?php
namespace App\DataFixtures;

use App\Entity\Topic;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $topicNames = [
            'School','Programming','Work','Family','Medicine','Politics','Army',
            'Vacation','Children','Animals','Travel','Technology','Friends',
            'Relationships','Teachers','Internet','Police','Neighbors'
        ];

        foreach ($topicNames as $name) {
            $topic = new Topic();
            $topic->setName($name);
            $manager->persist($topic);
        }

        $manager->flush();
    }
}
