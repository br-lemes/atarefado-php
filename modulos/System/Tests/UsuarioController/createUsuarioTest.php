<?php

declare(strict_types=1);

namespace Modulos\System\Tests;

use Modulos\System\Data\UsuarioData;
use Psr\Log\LoggerInterface;
use Tests\Utils\WebTestCase;

class createUsuarioTest extends WebTestCase
{
    private function createErrorLogin()
    {
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
    private function createErrorEmail()
    {
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
    private function createUsuario()
    {
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
    private function createInvalidParams()
    {
        $this->client->post('/api/usuarios', ['status' => 2]);
        $data = $this->client->getBodyArray();
        $this->assertEquals(400, $this->client->response->getStatusCode());
        $this->assertEquals([
            'perfil_id' => [
                'intVal' => 'O campo Perfil deve ser um número inteiro.',
                'min' => 'O campo Perfil deve ser um valor mínimo 1.'
            ],
            'login' => ['notBlank' => 'O campo Login é obrigatório.'],
            'senha' => ['notBlank' => 'O campo Senha é obrigatório.'],
            'status' => ['in' => 'O campo Status selecionado é inválido. Opções: { "0", "1" }.'],
        ], $data);
    }
    public function testCreateUsuario()
    {
        $this->client->setJwt($this->login(UsuarioData::ADMIN));
        $this->app->getContainer()->get(LoggerInterface::class)->setHandlers([]);
        $this->createErrorLogin();
        $this->createErrorEmail();
        $this->createUsuario();
        $this->createInvalidParams();
    }
}
