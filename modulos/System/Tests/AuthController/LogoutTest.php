<?php

declare(strict_types=1);

namespace Modulos\System\Tests;

use Modulos\System\Data\UsuarioData;
use Tests\Utils\WebTestCase;

class LogoutTest extends WebTestCase
{
    private function doLogout($code, $tokenId = null)
    {
        $this->client->post('/api/auth/logout', ['token_id' => $tokenId]);
        $this->assertEquals($code, $this->client->response->getStatusCode());
    }
    private function invalidTokenId()
    {
        $this->doLogout(400, 1000);
    }
    private function adminTokenId()
    {
        $this->doLogout(400, $this->info['admin']['token_id']);
    }
    private function noTokenId()
    {
        $this->doLogout(200);
    }
    private function userTokenId()
    {
        $this->doLogout(200, $this->info['user']['token_id']);
    }
    private function invalidParam()
    {
        $this->doLogout(400, 0);
    }
    public function testLogout()
    {
        $this->login(UsuarioData::ADMIN);
        $this->client->setJwt($this->login(UsuarioData::USER));
        $this->invalidTokenId();
        $this->adminTokenId();
        $this->noTokenId();
        $this->login(UsuarioData::USER);
        $this->client->setJwt($this->info['admin']['token_access']);
        $this->userTokenId();
        $this->invalidParam();
    }
}
