<?php

declare(strict_types=1);

namespace Modulos\System\Tests;

use Modulos\System\Data\PerfilData;
use Modulos\System\Data\UsuarioData;
use Psr\Log\LoggerInterface;
use Tests\Utils\WebTestCase;

class updatePerfilTest extends WebTestCase
{
    private function unauthorized()
    {
        $this->client->put('/api/perfis/2', ['status' => 0]);
        $this->assertEquals(401, $this->client->response->getStatusCode());
    }
    private function updateNotFound()
    {
        $this->client->put('/api/perfis/1000', []);
        $this->assertEquals(404, $this->client->response->getStatusCode());
    }
    private function updateMissingParams()
    {
        $this->client->put('/api/perfis/2', []);
        $data = $this->client->getBodyArray();
        $this->assertEquals(200, $this->client->response->getStatusCode());
        unset($data['created_at']);
        unset($data['updated_at']);
        $this->assertEquals(PerfilData::USER, $data);
    }
    private function updateStatus()
    {
        $this->client->put('/api/perfis/2', ['status' => 0]);
        $data = $this->client->getBodyArray();
        $this->assertEquals(200, $this->client->response->getStatusCode());
        unset($data['created_at']);
        unset($data['updated_at']);
        $expected = PerfilData::USER;
        $expected['status'] = 0;
        $this->assertEquals($expected, $data);
    }
    private function updateWithPost()
    {
        $perfilAdmin = PerfilData::ADMIN;
        $perfilAdmin['status'] = 0;
        $this->client->post('/api/perfis', $perfilAdmin);
        $data = $this->client->getBodyArray();
        $this->assertEquals(200, $this->client->response->getStatusCode());
        unset($data['created_at']);
        unset($data['updated_at']);
        $this->assertEquals($perfilAdmin, $data);
    }
    private function updateUser()
    {
        $this->client->setJwt($this->login(UsuarioData::USER));
        $this->client->put('/api/perfis/1', ['status' => 0]);
        $data = $this->client->getBodyArray();
        $this->assertEquals(401, $this->client->response->getStatusCode());
        $this->assertEquals(['message' => 'Não autorizado!', 'code' => 401], $data);
    }
    public function testUpdatePerfil()
    {
        $this->unauthorized();
        $this->client->setJwt($this->login(UsuarioData::ADMIN));
        $this->app->getContainer()->get(LoggerInterface::class)->setHandlers([]);
        $this->updateNotFound();
        $this->updateMissingParams();
        $this->updateStatus();
        $this->updateWithPost();
        $this->updateUser();
    }
}
