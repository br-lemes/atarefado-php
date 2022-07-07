<?php

declare(strict_types=1);

namespace Tests\integration;

use Psr\Log\LoggerInterface;
use Tests\Utils\WebTestCase;

class IndexControllerTest extends WebTestCase
{
    private function index()
    {
        $this->client->get('/');
        $data = $this->client->getBodyArray();
        $this->assertEquals(200, $this->client->response->getStatusCode());
        $this->assertEquals([
            'message' => 'Hello, World!',
        ], $data);
    }
    private function options()
    {
        $this->client->options('*');
        $data = $this->client->getBodyArray();
        $this->assertEquals(200, $this->client->response->getStatusCode());
        $this->assertEquals(null, $data);
    }
    private function notFound()
    {
        $this->client->get('/not-found');
        $data = $this->client->getBodyArray();
        $this->assertEquals(404, $this->client->response->getStatusCode());
        unset($data['trace']);
        $this->assertEquals([
            'message' => 'Not found.',
            'status' => 'error',
            'code' => 404,
            'class' => 'HttpNotFoundException',
        ], $data);
    }
    public function testIndex()
    {
        $this->index();
        $this->options();
        $this->app->getContainer()->get(LoggerInterface::class)->setHandlers([]);
        $this->notFound();
    }
}
