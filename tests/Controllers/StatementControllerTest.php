<?php

namespace App\Tests\Controllers;

use App\Entity\Statement;
use App\Entity\User;
use App\Service\StatementService;
use App\Tests\Fixtures\TokenFixture;
use App\Tests\Fixtures\UserMock;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class StatementControllerTest extends WebTestCase
{
    private MockObject $statementService;
    private MockObject $security;
    private MockObject $validator;
    private $client;
    private $token;
    private static $id;

    protected function setUp(): void
    {
        $this->statementService = $this->createMock(StatementService::class);
        $this->security = $this->createMock(Security::class);
        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->client = static::createClient();
        $this->token = TokenFixture::getToken($this->client);
    }

    public function testSaveValidData()
    {
        $data = ['name' => 'name579'];

        $this->client->request(
            'POST',
            '/api/client/statements/save',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'Bearer ' . $this->token],
            json_encode($data)
        );
        $response = $this->client->getResponse();
        $content = json_decode($response->getContent());
        self::$id = $content->id;
        $this->assertResponseIsSuccessful();
    }

   public function testSaveNoName()
    {
        $data = ['name' => ''];

        $this->client->request('POST',
            '/api/client/statements/save',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'Bearer ' . $this->token],
            json_encode($data)
        );

        $response = $this->client->getResponse();

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertJson($response->getContent());
        $this->assertStringContainsString('"This value should not be blank.', $response->getContent());
    }

     public function testSaveShortName()
     {
         $data = ['name' => 'qwer'];

         $this->client->request('POST',
             '/api/client/statements/save',
             [],
             [],
             ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'Bearer ' . $this->token],
             json_encode($data)
         );

         $response = $this->client->getResponse();

         $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
         $this->assertJson($response->getContent());
         $this->assertStringContainsString('This value is too short. It should have 5 characters or more.', $response->getContent());
     }

     public function testSignStatement()
     {
         $statementId = self::$id;
         $this->client->request('POST',
             '/api/client/statements/sign/' . $statementId,
             [],
             [],
             ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'Bearer ' . $this->token]
         );

         $response = $this->client->getResponse();
         $content = json_decode($response->getContent());
         $this->assertResponseIsSuccessful();
         $this->assertEquals('signed', $content->status);
     }

     public function testSignStatementNotPending()
     {
         $statementId = self::$id;
         $this->client->request('POST',
             '/api/client/statements/sign/' . $statementId,
             [],
             [],
             ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'Bearer ' . $this->token]
         );

         $response = $this->client->getResponse();
         $content = json_decode($response->getContent());
         $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
         $this->assertStringContainsString('Данная заявка не является черновиком', $content->error);
     }

     public function testDeleteStatement()
     {
         $statementId = self::$id;
         $this->client->request('DELETE',
             '/api/client/statements/delete/' . $statementId,
             [],
             [],
             ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'Bearer ' . $this->token]
         );

         $response = $this->client->getResponse();
         $content = json_decode($response->getContent());
         $this->assertResponseIsSuccessful();
         $this->assertEquals('deleted', $content->status);
     }

     public function testDeleteStatementNotValid()
     {
         $statementId = self::$id;
         $this->client->request('DELETE',
             '/api/client/statements/delete/' . $statementId,
             [],
             [],
             ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'Bearer ' . $this->token]
         );

         $response = $this->client->getResponse();
         $content = json_decode($response->getContent());
         $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
         $this->assertStringContainsString('Данная заявка не в статусе черновик или подписанный документ', $content->error);
     }

     public function testEditStatementNotValid()
     {
         $statementId = self::$id;
         $data = ['name' => 'sfdgsdfgsdfgdf'];
         $this->client->request('PATCH',
             '/api/client/statements/edit/' . $statementId,
             [],
             [],
             ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'Bearer ' . $this->token],
             json_encode($data)
         );

         $response = $this->client->getResponse();
         $content = json_decode($response->getContent());
         $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
         $this->assertStringContainsString('Нельзя редактировать заявление не в статусе Черновик', $content->error);
     }

     public function testEditStatement()
     {
         $data = ['name' => 'name579'];
         $this->client->request('POST',
             '/api/client/statements/save',
             [],
             [],
             ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'Bearer ' . $this->token],
             json_encode($data)
         );
         $response = $this->client->getResponse();
         $content = json_decode($response->getContent());
         $statementId = $content->id;
         $data = ['name' => 'newname579'];
         $this->client->request('PATCH',
             '/api/client/statements/edit/' . $statementId,
             [],
             [],
             ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'Bearer ' . $this->token],
             json_encode($data)
         );
         $response = $this->client->getResponse();
         $content = json_decode($response->getContent());
         $this->assertResponseIsSuccessful();
         $this->assertEquals('newname579', $content->name);
     }
}
