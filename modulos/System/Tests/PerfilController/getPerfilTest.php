<?php

declare(strict_types=1);

namespace Modulos\System\Tests;

use Modulos\System\Data\PerfilData;
use Modulos\System\Data\UsuarioData;
use Psr\Log\LoggerInterface;
use Tests\Utils\WebTestCase;

class getPerfilTest extends WebTestCase
{
    private function unauthorized()
    {
        $this->client->get('/api/perfis');
        $this->assertEquals(401, $this->client->response->getStatusCode());
        $this->client->get('/api/perfis/1');
        $this->assertEquals(401, $this->client->response->getStatusCode());
    }
    private function getAll()
    {
        $this->client->get('/api/perfis');
        $data = $this->client->getBodyArray();
        $this->assertEquals(200, $this->client->response->getStatusCode());
        foreach ($data as $key => $value) {
            unset($data[$key]['created_at']);
            unset($data[$key]['updated_at']);
        }
        $this->assertEquals(PerfilData::ALL_DESC, $data);
    }
    private function getOrder()
    {
        $this->client->get('/api/perfis', ['orderBy' => 'id:asc']);
        $data = $this->client->getBodyArray();
        $this->assertEquals(200, $this->client->response->getStatusCode());
        foreach ($data as $key => $value) {
            unset($data[$key]['created_at']);
            unset($data[$key]['updated_at']);
        }
        $this->assertEquals(PerfilData::ALL_ASC, $data);
    }
    private function getId()
    {
        $this->client->get('/api/perfis/1');
        $data = $this->client->getBodyArray();
        $this->assertEquals(200, $this->client->response->getStatusCode());
        unset($data['created_at']);
        unset($data['updated_at']);
        $this->assertEquals(PerfilData::ADMIN, $data);
    }
    private function getNotFound()
    {
        $this->app->getContainer()->get(LoggerInterface::class)->setHandlers([]);
        $this->client->get('/api/perfis/1000');
        $this->assertEquals(404, $this->client->response->getStatusCode());
    }
    private function getAllUser()
    {
        $this->client->get('/api/perfis');
        $data = $this->client->getBodyArray();
        $this->assertEquals(200, $this->client->response->getStatusCode());
        foreach ($data as $key => $value) {
            unset($data[$key]['created_at']);
            unset($data[$key]['updated_at']);
        }
        $this->assertEquals([PerfilData::USER], $data);
    }
    private function getIdUser()
    {
        $this->client->get('/api/perfis/1');
        $data = $this->client->getBodyArray();
        $this->assertEquals(401, $this->client->response->getStatusCode());
        $this->assertEquals(['message' => 'Não autorizado!', 'code' => 401], $data);
    }
    public function testGetPerfil()
    {
        $this->unauthorized();
        $this->client->setJwt($this->login(UsuarioData::ADMIN));
        $this->getAll();
        $this->getOrder();
        $this->getId();
        $this->getNotFound();
        $this->client->setJwt($this->login(UsuarioData::USER));
        $this->getAllUser();
        $this->getIdUser();
    }
}
