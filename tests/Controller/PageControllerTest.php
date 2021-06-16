<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\BrowserKit\Cookie;

class PageControllerTest extends WebTestCase
{
    protected function createAuthorizedClient($username)
    {
        $client = static::createClient();
        $container = static::$kernel->getContainer();
        $session = $container->get('session');
        $person = self::$kernel->getContainer()->get('doctrine')
            ->getRepository('App:User')
            ->findOneByUsername('admin');

        $token = new UsernamePasswordToken($person, null, 'main', $person->getRoles());
        $session->set('_security_main', serialize($token));
        $session->save();

        $client->getCookieJar()->set(new Cookie($session->getName(), $session->getId()));

        return $client;
    }

    public function testCanVisitPage1AsAdmin()
    {
        $client = $this->createAuthorizedClient('admin');
        $client->request('GET', '/page/1');
        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );
    }

    public function testCanVisitPage2AsAdmin()
    {
        $client = $this->createAuthorizedClient('admin');
        $client->request('GET', '/page/2');
        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );
    }

    public function testCannotVisitPage1WhenLoggedOut(): void
    {
        $client = static::createClient();
        $client->request('GET', '/page/1');
        $this->assertResponseStatusCodeSame(
            302,
            $client->getResponse()->getStatusCode()
        );
    }

    public function testRedirectToLogin(): void
    {
        $client = static::createClient();
        $client->request('GET', '/page/1');
        $client->followRedirect();
        $this->assertResponseStatusCodeSame(
            200,
            $client->getResponse()->getStatusCode()
        );
    }

    public function testCannotVisitPage2WhenLoggedOut(): void
    {
        $client = static::createClient();
        $client->request('GET', '/page/2');
        $this->assertResponseStatusCodeSame(
            302,
            $client->getResponse()->getStatusCode()
        );
    }

    public function testIndex(): void
    {
        if ($client = $this->createAuthorizedClient('admin'))
        {
            $client->request('GET', '/');
            $this->assertEquals(
                200,
                $client->getResponse()->getStatusCode()
            );
        }
        else{
            $this->assertEquals(
                302,
                $client->getResponse()->getStatusCode()
            );
        }
    }

    public function testPage1(): void
    {
        if ($client = $this->createAuthorizedClient('admin'))
        {
            $client->request('GET', '/page/1');
            $this->assertEquals(
                200,
                $client->getResponse()->getStatusCode()
            );
        }
    }

    public function testPage2(): void
    {
        if ($client = $this->createAuthorizedClient('admin'))
        {
            $client->request('GET', '/page/2');
            $this->assertEquals(
                200,
                $client->getResponse()->getStatusCode()
            );
        }
    }
}
