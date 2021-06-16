<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    public function testCannotCreateUserWithoutLogin(): void
    {
        $client = static::createClient();
        $request = $client->xmlHttpRequest('POST', '/users');
        $this->assertResponseStatusCodeSame(
            401,
            $client->getResponse()->getStatusCode()
        );
    }

    /*public function testCanCreateUserWhenLoggedIn(): void
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'adminpassword'
        ]);
        $body = array(
            'name' => 'Marv',
            'username' => 'Marv',
            'role' => 'ADMIN',
            'password' => '12345'
        );

        $client->request(
            'POST',
            '/users',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"name":"Fabien","username":"Fabs","role":"ADMIN","password":"12345"}'
        );

        $this->assertResponseStatusCodeSame(
            201,
            $client->getResponse()->getStatusCode()
        );
    }*/

    public function testCannotGetUsersWithoutLogin()
    {
        $client = static::createClient();
        $request = $client->xmlHttpRequest('GET', '/users');
        $this->assertResponseStatusCodeSame(
            401,
            $client->getResponse()->getStatusCode()
        );
    }

    public function testCanGetUsersWhenLoggedIn(): void
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'adminpassword'
        ]);

        $client->request(
            'GET',
            '/users',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
        );

        $this->assertResponseStatusCodeSame(
            200,
            $client->getResponse()->getStatusCode()
        );
    }

}
