<?php

declare(strict_types=1);

namespace Modulos\System\Tests;

use Modulos\System\Data\UsuarioData;
use Tests\Utils\WebTestCase;

class createPerfilTest extends WebTestCase
{
    private function createPerfil()
    {
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
    private function createInvalidParams()
    {
        $this->client->post('/api/perfis', ['status' => 2]);
        $data = $this->client->getBodyArray();
        $this->assertEquals(400, $this->client->response->getStatusCode());
        $this->assertEquals([
            'nome' => ['notBlank' => 'O campo Nome é obrigatório.'],
            'descricao' => ['notBlank' => 'O campo Descrição é obrigatório.'],
            'status' => ['in' => 'O campo Status selecionado é inválido. Opções: { "0", "1" }.'],
        ], $data);
    }
    public function testCreatePerfil()
    {
        $this->client->setJwt($this->login(UsuarioData::ADMIN));
        $this->createPerfil();
        $this->createInvalidParams();
    }
}
