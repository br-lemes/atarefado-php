<?php

declare(strict_types=1);

namespace Modulos\System\Tests;

use Modulos\System\Data\UsuarioData;
use Psr\Log\LoggerInterface;
use Tests\Utils\WebTestCase;

class updateUsuarioTest extends WebTestCase
{
    private function unauthorized()
    {
        $this->client->put('/api/usuarios/1', ['status' => 0]);
        $this->assertEquals(401, $this->client->response->getStatusCode());
    }
    private function updateNotFound()
    {
        $this->client->put('/api/usuarios/1000', []);
        $this->assertEquals(404, $this->client->response->getStatusCode());
    }
    private function updateMissingParams()
    {
        $this->client->put('/api/usuarios/1', []);
        $data = $this->client->getBodyArray();
        $this->assertEquals(200, $this->client->response->getStatusCode());
        unset($data['created_at']);
        unset($data['updated_at']);
        $expected = UsuarioData::ADMIN;
        unset($expected['perfil_nome']);
        unset($expected['perfil_descricao']);
        $this->assertEquals($expected, $data);
    }
    private function updateStatus()
    {
        $this->client->put('/api/usuarios/1', ['status' => 0]);
        $data = $this->client->getBodyArray();
        $this->assertEquals(200, $this->client->response->getStatusCode());
        unset($data['created_at']);
        unset($data['updated_at']);
        $expected = UsuarioData::ADMIN;
        $expected['status'] = 0;
        unset($expected['perfil_nome']);
        unset($expected['perfil_descricao']);
        $this->assertEquals($expected, $data);
    }
    private function updateWithPost()
    {
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
        $expected = UsuarioData::ADMIN;
        $expected['status'] = 0;
        unset($expected['perfil_nome']);
        unset($expected['perfil_descricao']);
        $this->assertEquals($expected, $data);
    }
    private function updateErrorLogin()
    {
        $this->client->put('/api/usuarios/2', ['login' => 'admin']);
        $data = $this->client->getBodyArray();
        $this->assertEquals(400, $this->client->response->getStatusCode());
        $this->assertEquals([
            'message' => 'Já existe usuário com este login!',
            'code' => 400,
        ], $data);
    }
    private function updateErrorEmail()
    {
        $this->client->put('/api/usuarios/2', ['email' => 'admin@localhost']);
        $data = $this->client->getBodyArray();
        $this->assertEquals(400, $this->client->response->getStatusCode());
        $this->assertEquals([
            'message' => 'Já existe usuário com este e-mail!',
            'code' => 400,
        ], $data);
    }
    private function updateUser()
    {
        $this->client->put('/api/usuarios/1', ['status' => 0]);
        $data = $this->client->getBodyArray();
        $this->assertEquals(401, $this->client->response->getStatusCode());
        $this->assertEquals(['message' => 'Não autorizado!', 'code' => 401], $data);
    }
    public function testUpdateUsuario()
    {
        $this->unauthorized();
        $this->client->setJwt($this->login(UsuarioData::ADMIN));
        $this->app->getContainer()->get(LoggerInterface::class)->setHandlers([]);
        $this->updateNotFound();
        $this->updateMissingParams();
        $this->updateStatus();
        $this->updateWithPost();
        $this->updateErrorLogin();
        $this->updateErrorEmail();
        $this->client->setJwt($this->login(UsuarioData::USER));
        $this->updateUser();
    }
}
