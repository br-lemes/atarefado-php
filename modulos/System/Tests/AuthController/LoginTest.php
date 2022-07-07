<?php

declare(strict_types=1);

namespace Modulos\System\Tests;

use Modulos\System\Data\UsuarioData;
use Tests\Utils\WebTestCase;

class LoginTest extends WebTestCase
{
    private function doLogin($login, $senha, $code, $info)
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
    private function loginAdmin()
    {
        $this->doLogin('admin', 'admin', 200, UsuarioData::ADMIN);
    }
    private function loginUser()
    {
        $this->doLogin('user', 'user', 200, UsuarioData::USER);
    }
    private function loginInvalidUser()
    {
        $this->doLogin('adam', 'adam', 401, [
            'message' => 'Usuário ou senha incorreta!',
            'code' => 401,
        ]);
    }
    private function loginInvalidPassword()
    {
        $this->doLogin('admin', '123456', 401, [
            'message' => 'Usuário ou senha incorreta!',
            'code' => 401,
        ]);
    }
    private function loginDisabled()
    {
        $this->doLogin('test', 'test', 401, [
            'message' => 'Usuário inativo, entre contato com o suporte!',
            'code' => 401,
        ]);
    }
    private function loginMissingParams()
    {
        $this->doLogin('', '', 400, [
            'login' => ['notBlank' => 'O campo login é obrigatório.'],
            'senha' => ['notBlank' => 'O campo senha é obrigatório.'],
        ]);
    }
    public function testLogin()
    {
        $this->loginAdmin();
        $this->loginUser();
        $this->loginInvalidUser();
        $this->loginInvalidPassword();
        $this->loginDisabled();
        $this->loginMissingParams();
    }
}
