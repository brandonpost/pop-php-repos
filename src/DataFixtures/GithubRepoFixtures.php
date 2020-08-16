<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\GithubRepo;

class GithubRepoFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $repo = new GithubRepo();
        $repo->setRepositoryId(12345);
        $repo->setName('TestRepo');
        $repo->setUrl('https://brandonpost.com');
        $repo->setCreatedDate('2020-08-15T23:45:23Z');
        $repo->setLastPushDate('2020-08-15T23:45:23Z');
        $repo->setDescription('Test description.');
        $repo->setStars(10001);
        $manager->persist($product);

        $manager->flush();
    }
}
