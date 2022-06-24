<?php

declare(strict_types=1);

namespace Modulos\System\Tests;

use Tests\Utils\WebTestCase;
use Modulos\System\Data\UsuarioData;

class AuthControllerTest extends WebTestCase
{
    private function testLogin($login, $senha, $code, $info)
    {
        $this->client->post('/api/auth/login', ['login' => $login, 'senha' => $senha]);
        $data = $this->client->getBodyArray();

        $this->assertEquals($code, $this->client->response->getStatusCode());
        if ($code == 200) {
            $this->assertEquals(3, count($data));
            unset($data['usuario']['created_at']);
            unset($data['usuario']['updated_at']);
            $this->assertEquals($info, $data['usuario']);
            return;
        }
        $this->assertEquals($info, $data);
    }
    public function testLoginAdmin()
    {
        $this->testLogin('admin', 'admin', 200, UsuarioData::ADMIN);
    }
    public function testLoginUser()
    {
        $this->testLogin('user', 'user', 200, UsuarioData::USER);
    }
    public function testLoginInvalidUser()
    {
        $this->testLogin('adam', 'adam', 401, [
            'message' => 'Usuário ou senha incorreta!',
            'code' => 401,
        ]);
    }
    public function testLoginInvalidPassword()
    {
        $this->testLogin('admin', '123456', 401, [
            'message' => 'Usuário ou senha incorreta!',
            'code' => 401,
        ]);
    }
    public function testLoginInvalidParams()
    {
        $this->testLogin('', '', 400, [
            'login' => ['notBlank' => 'O campo login é obrigatório.'],
            'senha' => ['notBlank' => 'O campo senha é obrigatório.'],
        ]);
    }
    public function testLoginDisabled()
    {
        $this->testLogin('test', 'test', 401, [
            'message' => 'Usuário inativo, entre contato com o suporte!',
            'code' => 401,
        ]);
    }
    public function testInfo()
    {
        $this->client->setJwt($this->login(UsuarioData::ADMIN));
        $this->client->get('/api/auth/info');
        $data = $this->client->getBodyArray();
        $this->assertEquals(200, $this->client->response->getStatusCode());
        unset($data['created_at']);
        unset($data['updated_at']);
        $this->assertEquals(UsuarioData::ADMIN, $data);
    }
}
