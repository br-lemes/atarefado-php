<?php

declare(strict_types=1);

namespace Modulos\System\Tests;

use Modulos\System\Data\PerfilData;
use Modulos\System\Data\UsuarioData;
use Psr\Log\LoggerInterface;
use Tests\Utils\WebTestCase;

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
        $this->app->getContainer()->get(LoggerInterface::class)->setHandlers([]);
        $this->client->setJwt($this->token);
        $this->client->get('/api/perfis/1000');
        $this->assertEquals(404, $this->client->response->getStatusCode());
    }
    public function testPerfisCreateNotBlank()
    {
        $this->client->setJwt($this->token);
        $this->client->post('/api/perfis', []);
        $data = $this->client->getBodyArray();
        $this->assertEquals(400, $this->client->response->getStatusCode());
        $this->assertEquals([
            'nome' => ['notBlank' => 'O campo Nome é obrigatório.'],
            'descricao' => ['notBlank' => 'O campo Descrição é obrigatório.'],
        ], $data);
    }
    public function testPerfisCreateIn()
    {
        $this->client->setJwt($this->token);
        $this->client->post('/api/perfis', ['nome' => '.', 'descricao' => '.', 'status' => 2]);
        $data = $this->client->getBodyArray();
        $this->assertEquals(400, $this->client->response->getStatusCode());
        $this->assertEquals([
            'status' => ['in' => 'O campo Status selecionado é inválido. Opções: { "0", "1" }.'],
        ], $data);
    }
    public function testPerfisCreate()
    {
        $this->client->setJwt($this->token);
        $this->client->post('/api/perfis', ['nome' => 'Teste', 'descricao' => 'Perfil de teste']);
        $data = $this->client->getBodyArray();
        $this->assertEquals(200, $this->client->response->getStatusCode());
        unset($data['created_at']);
        unset($data['updated_at']);
        $this->assertEquals([
            'id' => 3,
            'nome' => 'Teste',
            'descricao' => 'Perfil de teste',
            'status' => 1,
            'token_id' => 1,
        ], $data);
    }
    public function testPerfisUpdateNotFound()
    {
        $this->app->getContainer()->get(LoggerInterface::class)->setHandlers([]);
        $this->client->setJwt($this->token);
        $this->client->put('/api/perfis/1000', []);
        $this->assertEquals(404, $this->client->response->getStatusCode());
    }
    public function testPerfisUpdateBlank()
    {
        $this->client->setJwt($this->token);
        $this->client->put('/api/perfis/2', []);
        $data = $this->client->getBodyArray();
        $this->assertEquals(200, $this->client->response->getStatusCode());
        unset($data['created_at']);
        unset($data['updated_at']);
        $this->assertEquals([
            'id' => 2,
            'nome' => 'Usuário',
            'descricao' => 'Acesso limitado ao sistema',
            'status' => 1,
            'token_id' => 1,
        ], $data);
    }
    public function testPerfisUpdate()
    {
        $this->client->setJwt($this->token);
        $this->client->put('/api/perfis/2', ['status' => 0]);
        $data = $this->client->getBodyArray();
        $this->assertEquals(200, $this->client->response->getStatusCode());
        unset($data['created_at']);
        unset($data['updated_at']);
        $this->assertEquals([
            'id' => 2,
            'nome' => 'Usuário',
            'descricao' => 'Acesso limitado ao sistema',
            'status' => 0,
            'token_id' => 1,
        ], $data);
    }
    public function testPerfisUpdatePost()
    {
        $this->client->setJwt($this->token);
        $this->client->post(
            '/api/perfis',
            [
                'id' => 1,
                'nome' => 'Administrador',
                'descricao' => 'Acesso completo ao sistema',
                'status' => 0,
            ]
        );
        $data = $this->client->getBodyArray();
        $this->assertEquals(200, $this->client->response->getStatusCode());
        unset($data['created_at']);
        unset($data['updated_at']);
        $this->assertEquals([
            'id' => 1,
            'nome' => 'Administrador',
            'descricao' => 'Acesso completo ao sistema',
            'status' => 0,
            'token_id' => 1,
        ], $data);
    }
}
