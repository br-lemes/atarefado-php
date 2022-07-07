<?php

declare(strict_types=1);

namespace Modulos\System\Tests;

use Modulos\System\Data\UsuarioData;
use Modulos\System\Models\Usuario;
use Tests\Utils\WebTestCase;

class InfoTest extends WebTestCase
{
    private function info()
    {
        $this->client->get('/api/auth/info');
        $data = $this->client->getBodyArray();
        $this->assertEquals(200, $this->client->response->getStatusCode());
        unset($data['created_at']);
        unset($data['updated_at']);
        $this->assertEquals(UsuarioData::ADMIN, $data);
    }
    private function infoBrokenDB()
    {
        Usuario::truncate();
        $this->client->get('/api/auth/info');
        $data = $this->client->getBodyArray();
        $this->assertEquals(500, $this->client->response->getStatusCode());
        $this->assertEquals(['message' => 'Usuário não encontrado!', 'code' => 500], $data);
    }
    public function testInfo()
    {
        $this->client->setJwt($this->login(UsuarioData::ADMIN));
        $this->info();
        $this->infoBrokenDB();
    }
}
