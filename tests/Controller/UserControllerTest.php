<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    public function testCreateUser(): void
    {
        $client = static::createClient();

        // Enviar JSON correctamente con método request()
        $client->request(
            'POST',
            '/api/users',
            [], // parámetros
            [], // archivos
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'name' => 'John',
                'email' => 'john@example.com',
            ])
        );

        $this->assertResponseIsSuccessful();

        // Obtener la respuesta
        $response = $client->getResponse();
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));

        $data = json_decode($response->getContent(), true);
        $this->assertIsArray($data);
    }
}
