<?php

declare(strict_types=1);

namespace Tests\integration;

use Tests\Utils\WebTestCase;
use Modulos\System\Data\UsuarioData;

class UsuarioControllerTest extends WebTestCase
{
    protected $token;
    protected function setUp(): void
    {
        parent::setUp();
        $this->token = $this->login(UsuarioData::ADMIN);
    }
    public function testUnauthorized()
    {
        $this->client->get('/api/usuarios');
        $this->assertEquals(401, $this->client->response->getStatusCode());
        $this->client->get('/api/usuarios/1');
        $this->assertEquals(401, $this->client->response->getStatusCode());
    }
    public function testUsuarios()
    {
        $this->client->setJwt($this->token);
        $this->client->get('/api/usuarios');
        $data = $this->client->getBodyArray();
        $this->assertEquals(200, $this->client->response->getStatusCode());
        foreach ($data as $key => $value) {
            unset($data[$key]['created_at']);
            unset($data[$key]['updated_at']);
        }
        $this->assertEquals(UsuarioData::ALL_DESC, $data);
    }
    public function testUsuariosOrder()
    {
        $this->client->setJwt($this->token);
        $this->client->get('/api/usuarios', ['orderBy' => 'id:asc']);
        $data = $this->client->getBodyArray();
        $this->assertEquals(200, $this->client->response->getStatusCode());
        foreach ($data as $key => $value) {
            unset($data[$key]['created_at']);
            unset($data[$key]['updated_at']);
        }
        $this->assertEquals(UsuarioData::ALL_ASC, $data);
    }
    public function testUsuariosOrders()
    {
        $this->client->setJwt($this->token);
        $this->client->get('/api/usuarios', ['orderBy' => ['perfil_id:desc', 'status']]);
        $data = $this->client->getBodyArray();
        $this->assertEquals(200, $this->client->response->getStatusCode());
        foreach ($data as $key => $value) {
            unset($data[$key]['created_at']);
            unset($data[$key]['updated_at']);
        }
        $this->assertEquals([UsuarioData::TEST, UsuarioData::USER, UsuarioData::ADMIN], $data);
    }
    public function testUsuariosDisabled()
    {
        $this->client->setJwt($this->token);
        $this->client->get('/api/usuarios', ['status' => 0]);
        $data = $this->client->getBodyArray();
        $this->assertEquals(200, $this->client->response->getStatusCode());
        foreach ($data as $key => $value) {
            unset($data[$key]['created_at']);
            unset($data[$key]['updated_at']);
        }
        $this->assertEquals([UsuarioData::TEST], $data);
    }
    public function testUsuariosId()
    {
        $this->client->setJwt($this->token);
        $this->client->get('/api/usuarios/1');
        $data = $this->client->getBodyArray();
        $this->assertEquals(200, $this->client->response->getStatusCode());
        unset($data['created_at']);
        unset($data['updated_at']);
        $this->assertEquals(UsuarioData::ADMIN, $data);
    }
    public function testUsuariosIds()
    {
        $this->client->setJwt($this->token);
        $this->client->get('/api/usuarios', ['id' => [1, 3]]);
        $data = $this->client->getBodyArray();
        $this->assertEquals(200, $this->client->response->getStatusCode());
        foreach ($data as $key => $value) {
            unset($data[$key]['created_at']);
            unset($data[$key]['updated_at']);
        }
        $this->assertEquals([UsuarioData::TEST, UsuarioData::ADMIN], $data);
    }
    public function testUsuariosNotFound()
    {
        $this->client->setJwt($this->token);
        $this->client->get('/api/usuarios/0');
        $this->assertEquals(404, $this->client->response->getStatusCode());
    }
}
