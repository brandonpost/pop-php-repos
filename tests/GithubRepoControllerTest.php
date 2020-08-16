<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GithubRepoControllerTest extends WebTestCase
{
    public function testShowListPage()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        // Make sure the request was successful.
        $this->assertResponseIsSuccessful();
        // Only one h1 tag on the page.
        $this->assertCount(1, $crawler->filter('h1'));
        // H1 tag contains 'Popular PHP Repositories'.
        $this->assertSelectorTextContains('h1', 'Popular PHP Repositories');
    }

    public function testShowDetailPage()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/repo/1863329');

        // Make sure the request was successful.
        $this->assertResponseIsSuccessful();
        // Only one h1 tag on the page.
        $this->assertCount(1, $crawler->filter('h1'));
        // H1 tag contains 'laravel'.
        $this->assertSelectorTextContains('h1', 'laravel');
        // At least one span.item-title.
        $this->assertGreaterThan(0, $crawler->filter('span.item-title')->count());
    }

    public function testPageNotFound()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/repo/1234');

        // Make sure 404 was returned.
        $this->assertTrue($client->getResponse()->isNotFound());
    }
}
