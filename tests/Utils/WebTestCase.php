<?php

declare(strict_types=1);

namespace Tests\Utils;

class WebTestCase extends TestCase
{
    /** @var \Slim\App */
    protected $app;

    /** @var \Tests\Utils\WebTestClient */
    protected $client;

    protected function setUp(): void
    {
        $this->app = $this->getAppInstance();
        $this->client = new WebTestClient($this->app);
    }
    protected function login($user)
    {
        $this->client->post(
            '/api/auth/login',
            ['login' => $user['login'], 'senha' => $user['login']]
        );
        $data = $this->client->getBodyArray();
        $this->assertEquals(200, $this->client->response->getStatusCode());
        return $data['token_access'];
    }
}
