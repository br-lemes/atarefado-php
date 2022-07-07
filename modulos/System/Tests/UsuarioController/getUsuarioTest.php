<?php

declare(strict_types=1);

namespace Modulos\System\Tests;

use Modulos\System\Data\UsuarioData;
use Psr\Log\LoggerInterface;
use Tests\Utils\WebTestCase;

class getUsuarioTest extends WebTestCase
{
    private function unauthorized()
    {
        $this->client->get('/api/usuarios');
        $this->assertEquals(401, $this->client->response->getStatusCode());
        $this->client->get('/api/usuarios/1');
        $this->assertEquals(401, $this->client->response->getStatusCode());
    }
    private function getAll()
    {
        $this->client->get('/api/usuarios');
        $data = $this->client->getBodyArray();
        $this->assertEquals(200, $this->client->response->getStatusCode());
        foreach ($data as $key => $value) {
            unset($data[$key]['created_at']);
            unset($data[$key]['updated_at']);
        }
        $this->assertEquals(UsuarioData::ALL_DESC, $data);
    }
    private function getOrder()
    {
        $this->client->get('/api/usuarios', ['orderBy' => 'id:asc']);
        $data = $this->client->getBodyArray();
        $this->assertEquals(200, $this->client->response->getStatusCode());
        foreach ($data as $key => $value) {
            unset($data[$key]['created_at']);
            unset($data[$key]['updated_at']);
        }
        $this->assertEquals(UsuarioData::ALL_ASC, $data);
    }
    private function getMoreOrder()
    {
        $this->client->get('/api/usuarios', ['orderBy' => ['perfil_id:desc', 'status']]);
        $data = $this->client->getBodyArray();
        $this->assertEquals(200, $this->client->response->getStatusCode());
        foreach ($data as $key => $value) {
            unset($data[$key]['created_at']);
            unset($data[$key]['updated_at']);
        }
        $this->assertEquals([UsuarioData::TEST, UsuarioData::USER, UsuarioData::ADMIN], $data);
    }
    private function getDisabled()
    {
        $this->client->get('/api/usuarios', ['status' => 0]);
        $data = $this->client->getBodyArray();
        $this->assertEquals(200, $this->client->response->getStatusCode());
        foreach ($data as $key => $value) {
            unset($data[$key]['created_at']);
            unset($data[$key]['updated_at']);
        }
        $this->assertEquals([UsuarioData::TEST], $data);
    }
    private function getId()
    {
        $this->client->get('/api/usuarios/1');
        $data = $this->client->getBodyArray();
        $this->assertEquals(200, $this->client->response->getStatusCode());
        unset($data['created_at']);
        unset($data['updated_at']);
        $this->assertEquals(UsuarioData::ADMIN, $data);
    }
    private function getIds()
    {
        $this->client->get('/api/usuarios', ['id' => [1, 3]]);
        $data = $this->client->getBodyArray();
        $this->assertEquals(200, $this->client->response->getStatusCode());
        foreach ($data as $key => $value) {
            unset($data[$key]['created_at']);
            unset($data[$key]['updated_at']);
        }
        $this->assertEquals([UsuarioData::TEST, UsuarioData::ADMIN], $data);
    }
    private function getNotFound()
    {
        $this->app->getContainer()->get(LoggerInterface::class)->setHandlers([]);
        $this->client->get('/api/usuarios/1000');
        $this->assertEquals(404, $this->client->response->getStatusCode());
    }
    private function getAllUser()
    {
        $this->client->get('/api/usuarios');
        $data = $this->client->getBodyArray();
        $this->assertEquals(200, $this->client->response->getStatusCode());
        foreach ($data as $key => $value) {
            unset($data[$key]['created_at']);
            unset($data[$key]['updated_at']);
        }
        $this->assertEquals([UsuarioData::USER], $data);
    }
    private function getIdUser()
    {
        $this->client->get('/api/usuarios/1');
        $data = $this->client->getBodyArray();
        $this->assertEquals(401, $this->client->response->getStatusCode());
        $this->assertEquals(['message' => 'Não autorizado!', 'code' => 401], $data);
    }
    public function testGetUsuario()
    {
        $this->unauthorized();
        $this->client->setJwt($this->login(UsuarioData::ADMIN));
        $this->getAll();
        $this->getOrder();
        $this->getMoreOrder();
        $this->getDisabled();
        $this->getId();
        $this->getIds();
        $this->getNotFound();
        $this->client->setJwt($this->login(UsuarioData::USER));
        $this->getAllUser();
        $this->getIdUser();
    }
}
