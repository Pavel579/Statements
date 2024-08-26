<?php

namespace App\Tests\Fixtures;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpClient\HttpClient;

class TokenFixture extends WebTestCase
{
    private static ?string $token = null;

    public static function getToken($client): string
    {
        if (self::$token === null) {
            //$client = static::createClient();

            // Отправка запроса для получения токена
            $client->request('POST', '/api/login_check', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
                'email' => 'pavel@mail.ru',
                'password' => '123',
            ]));
            //$client->request('GET', '/test', [], [], ['CONTENT_TYPE' => 'application/json']);

            $response = $client->getResponse();
           // var_dump("579 ".$response->getContent());
            $data = json_decode($response->getContent(), true);
            self::$token = $data['token'] ?? '';
        }

        return self::$token;
    }
}