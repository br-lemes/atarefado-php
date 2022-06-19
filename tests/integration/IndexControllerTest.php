<?php

declare(strict_types=1);

namespace Tests\integration;

use Tests\Utils\WebTestCase;

class IndexControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $this->client->get('/');
        $data = $this->client->getBodyArray();

        $this->assertEquals(200, $this->client->response->getStatusCode());
        $this->assertEquals([
            'message' => 'Hello, World!',
        ], $data);
    }
}
