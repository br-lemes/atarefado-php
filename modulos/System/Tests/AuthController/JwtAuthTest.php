<?php

declare(strict_types=1);

namespace Modulos\System\Tests;

use Modulos\System\Data\UsuarioData;
use Modulos\System\Models\Token;
use Tests\Utils\WebTestCase;

class JwtAuthTest extends WebTestCase
{
    private function unauthorized()
    {
        $this->client->get('/api/auth/info');
        $this->assertEquals(401, $this->client->response->getStatusCode());
    }
    private function invalid()
    {
        $this->client->setJwt('invalid');
        $this->unauthorized();
    }
    private function brokenDB()
    {
    	$this->client->setJwt($this->login(UsuarioData::ADMIN));
        Token::truncate();
        $this->unauthorized();
    }
    public function testJwtAuth()
    {
        $this->unauthorized();
        $this->invalid();
        $this->brokenDB();
    }
}
