<?php

declare(strict_types=1);

namespace Modulos\System\Tests;

use Tests\Utils\WebTestCase;
use Modulos\System\Data\PerfilData;
use Modulos\System\Data\UsuarioData;

class PerfilControllerTest extends WebTestCase
{
    protected $token;
    protected function setUp(): void
    {
        parent::setUp();
        $this->token = $this->login(UsuarioData::ADMIN);
    }
    public function testUnauthorized()
    {
        $this->client->get('/api/perfis');
        $this->assertEquals(401, $this->client->response->getStatusCode());
        $this->client->get('/api/perfis/1');
        $this->assertEquals(401, $this->client->response->getStatusCode());
    }
    public function testPerfis()
    {
        $this->client->setJwt($this->token);
        $this->client->get('/api/perfis');
        $data = $this->client->getBodyArray();
        $this->assertEquals(200, $this->client->response->getStatusCode());
        foreach ($data as $key => $value) {
            unset($data[$key]['created_at']);
            unset($data[$key]['updated_at']);
        }
        $this->assertEquals(PerfilData::ALL_DESC, $data);
    }
    public function testPerfisOrder()
    {
        $this->client->setJwt($this->token);
        $this->client->get('/api/perfis', ['orderBy' => 'id:asc']);
        $data = $this->client->getBodyArray();
        $this->assertEquals(200, $this->client->response->getStatusCode());
        foreach ($data as $key => $value) {
            unset($data[$key]['created_at']);
            unset($data[$key]['updated_at']);
        }
        $this->assertEquals(PerfilData::ALL_ASC, $data);
    }
    public function testPerfisId()
    {
        $this->client->setJwt($this->token);
        $this->client->get('/api/perfis/1');
        $data = $this->client->getBodyArray();
        $this->assertEquals(200, $this->client->response->getStatusCode());
        unset($data['created_at']);
        unset($data['updated_at']);
        $this->assertEquals(PerfilData::ADMIN, $data);
    }
    public function testPerfisNotFound()
    {
        $this->client->setJwt($this->token);
        $this->client->get('/api/perfis/0');
        $this->assertEquals(404, $this->client->response->getStatusCode());
    }
}
