<?php

declare(strict_types=1);

namespace Modulos\System\Tests;

use Modulos\System\Data\UsuarioData;
use Tests\Utils\WebTestCase;

class RefreshTest extends WebTestCase
{
    private function snippetNotRefresh()
    {
        $data = $this->client->getBodyArray();
        $this->assertEquals(401, $this->client->response->getStatusCode());
        $this->assertEquals(
            ['message' => 'Não foi possível atualizar o token!', 'code' => 401],
            $data
        );
    }
    private function refreshNotAToken()
    {
        $this->client->post('/api/auth/refresh', ['token_refresh' => 'invalid']);
        $this->snippetNotRefresh();
    }
    private function refreshInvalidToken()
    {
        $this->client->post(
            '/api/auth/refresh',
            ['token_refresh' => $this->info['admin']['token_access']]
        );
        $this->snippetNotRefresh();
    }
    private function refresh()
    {
        sleep(1);
        $this->client->post(
            '/api/auth/refresh',
            ['token_refresh' => $this->info['admin']['token_refresh']]
        );
        $data = $this->client->getBodyArray();
        $this->assertEquals(200, $this->client->response->getStatusCode());
        $this->assertEquals(1, count($data));
        $this->info['admin']['token_access'] = $data['token_access'];
    }
    private function rejectOldToken()
    {
        $this->client->get('/api/auth/info');
        $this->assertEquals(401, $this->client->response->getStatusCode());
        $this->client->setJwt($this->info['admin']['token_access']);
        $this->client->get('/api/auth/info');
        $this->assertEquals(200, $this->client->response->getStatusCode());
    }
    private function refreshMissingParams()
    {
        $this->client->post('/api/auth/refresh');
        $data = $this->client->getBodyArray();
        $this->assertEquals(400, $this->client->response->getStatusCode());
        $this->assertEquals(['token_refresh' => ['notBlank' => 'O campo Token Refresh é obrigatório.']], $data);
    }
    public function testRefresh()
    {
        $this->client->setJwt($this->login(UsuarioData::ADMIN));
        $this->refreshNotAToken();
        $this->refreshInvalidToken();
        $this->refresh();
        $this->rejectOldToken();
        $this->refreshMissingParams();
    }
}
