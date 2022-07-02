<?php

declare(strict_types=1);

namespace Modulos\System\Tests;

use Modulos\System\Data\UsuarioData;
use Psr\Log\LoggerInterface;
use Tests\Utils\WebTestCase;

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
        $this->app->getContainer()->get(LoggerInterface::class)->setHandlers([]);
        $this->client->setJwt($this->token);
        $this->client->get('/api/usuarios/1000');
        $this->assertEquals(404, $this->client->response->getStatusCode());
    }
    public function testUsuariosCreateNotBlank()
    {
        $this->client->setJwt($this->token);
        $this->client->post('/api/usuarios', []);
        $data = $this->client->getBodyArray();
        $this->assertEquals(400, $this->client->response->getStatusCode());
        $this->assertEquals([
            'perfil_id' => [
                'intVal' => 'O campo Perfil deve ser um número inteiro.',
                'min' => 'O campo Perfil deve ser um valor mínimo 1.'
            ],
            'login' => ['notBlank' => 'O campo Login é obrigatório.'],
            'senha' => ['notBlank' => 'O campo Senha é obrigatório.'],
        ], $data);
    }
    public function testUsuariosCreateIn()
    {
        $this->client->setJwt($this->token);
        $this->client->post('/api/usuarios', [
            'perfil_id' => 2, 'login' => '.', 'senha' => '.', 'status' => 2,
        ]);
        $data = $this->client->getBodyArray();
        $this->assertEquals(400, $this->client->response->getStatusCode());
        $this->assertEquals([
            'status' => ['in' => 'O campo Status selecionado é inválido. Opções: { "0", "1" }.'],
        ], $data);
    }
    public function testUsuariosCreate()
    {
        $this->client->setJwt($this->token);
        $this->client->post('/api/usuarios', [
            'perfil_id' => 2, 'login' => 'phpunit', 'senha' => 'phpunit',
        ]);
        $data = $this->client->getBodyArray();
        $this->assertEquals(200, $this->client->response->getStatusCode());
        unset($data['created_at']);
        unset($data['updated_at']);
        $this->assertEquals([
            'id' => 4,
            'perfil_id' => 2,
            'login' => 'phpunit',
            'status' => 1,
            'token_id' => 1,
        ], $data);
    }
    public function testUsuariosUpdateNotFound()
    {
        $this->app->getContainer()->get(LoggerInterface::class)->setHandlers([]);
        $this->client->setJwt($this->token);
        $this->client->put('/api/usuarios/1000', []);
        $this->assertEquals(404, $this->client->response->getStatusCode());
    }
    public function testUsuariosUpdateBlank()
    {
        $this->client->setJwt($this->token);
        $this->client->put('/api/usuarios/2', []);
        $data = $this->client->getBodyArray();
        $this->assertEquals(200, $this->client->response->getStatusCode());
        unset($data['created_at']);
        unset($data['updated_at']);
        $this->assertEquals([
            'id' => 2,
            'perfil_id' => 2,
            'nome' => 'Usuário',
            'login' => 'user',
            'email' => 'user@localhost',
            'status' => 1,
            'token_id' => 1,
        ], $data);
    }
    public function testUsuariosUpdate()
    {
        $this->client->setJwt($this->token);
        $this->client->put('/api/usuarios/2', ['status' => 0]);
        $data = $this->client->getBodyArray();
        $this->assertEquals(200, $this->client->response->getStatusCode());
        unset($data['created_at']);
        unset($data['updated_at']);
        $this->assertEquals([
            'id' => 2,
            'perfil_id' => 2,
            'nome' => 'Usuário',
            'login' => 'user',
            'email' => 'user@localhost',
            'status' => 0,
            'token_id' => 1,
        ], $data);
    }
    public function testUsuariosUpdatePost()
    {
        $this->client->setJwt($this->token);
        $this->client->post(
            '/api/usuarios',
            [
                'id' => 1,
                'perfil_id' => 1,
                'login' => 'admin',
                'senha' => 'admin',
                'status' => 0,
            ]
        );
        $data = $this->client->getBodyArray();
        $this->assertEquals(200, $this->client->response->getStatusCode());
        unset($data['created_at']);
        unset($data['updated_at']);
        $this->assertEquals([
            'id' => 1,
            'perfil_id' => 1,
            'nome' => 'Administrador',
            'login' => 'admin',
            'email' => 'admin@localhost',
            'status' => 0,
            'token_id' => 1,
        ], $data);
    }
    public function testUsuariosUpdateLogin()
    {
        $this->app->getContainer()->get(LoggerInterface::class)->setHandlers([]);
        $this->client->setJwt($this->token);
        $this->client->put('/api/usuarios/2', ['login' => 'admin']);
        $data = $this->client->getBodyArray();
        $this->assertEquals(400, $this->client->response->getStatusCode());
        $this->assertEquals([
            'message' => 'Já existe usuário com este login!',
            'code' => 400,
        ], $data);
    }
    public function testUsuariosCreateLogin()
    {
        $this->app->getContainer()->get(LoggerInterface::class)->setHandlers([]);
        $this->client->setJwt($this->token);
        $this->client->post(
            '/api/usuarios',
            ['perfil_id' => 2, 'login' => 'admin', 'senha' => 'admin']
        );
        $data = $this->client->getBodyArray();
        $this->assertEquals(400, $this->client->response->getStatusCode());
        $this->assertEquals([
            'message' => 'Já existe usuário com este login!',
            'code' => 400,
        ], $data);
    }
    public function testUsuariosCreateEmail()
    {
        $this->app->getContainer()->get(LoggerInterface::class)->setHandlers([]);
        $this->client->setJwt($this->token);
        $this->client->post(
            '/api/usuarios',
            ['perfil_id' => 2, 'login' => '.', 'senha' => '.', 'email' => 'admin@localhost']
        );
        $data = $this->client->getBodyArray();
        $this->assertEquals(400, $this->client->response->getStatusCode());
        $this->assertEquals([
            'message' => 'Já existe usuário com este e-mail!',
            'code' => 400,
        ], $data);
    }
}
