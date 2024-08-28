<?php

namespace App\Tests\Fixtures;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TokenFixture extends WebTestCase
{
    private static ?string $token = null;

    public static function getToken($client): string
    {
        if (self::$token === null) {

            // Отправка запроса для получения токена
            $client->request('POST', '/api/login_check', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
                'email' => 'pavel@mail.ru',
                'password' => '123',
            ]));

            $response = $client->getResponse();
            $data = json_decode($response->getContent(), true);
            self::$token = $data['token'] ?? '';
        }

        return self::$token;
    }
}