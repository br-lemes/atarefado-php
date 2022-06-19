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
}
