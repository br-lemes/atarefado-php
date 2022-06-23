<?php

declare(strict_types=1);

namespace Tests\Utils;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Slim\Psr7\Factory\StreamFactory;
use Slim\Psr7\Headers;
use Slim\Psr7\Request as SlimRequest;
use Slim\Psr7\Uri;
use DI\ContainerBuilder;
use Slim\App;
use Phinx\Config\Config;
use Phinx\Migration\Manager;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;

class TestCase extends PHPUnitTestCase
{
    protected function getAppInstance()
    {
        require __DIR__ . '/../../vendor/autoload.php';
        $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/../..', '.env.test');
        $dotenv->load();
        $dotenv->required(['DB_DATABASE', 'JWT_SECRET']);
        $settings = require __DIR__ . '/../../src/app/settings.php';
        $containerBuilder = new ContainerBuilder();
        $containerDefinitions = require __DIR__ . '/../../src/app/container.php';
        $containerBuilder->addDefinitions($containerDefinitions);
        $container = $containerBuilder->build();
        $app = $container->get(App::class);
        require __DIR__ . '/../../src/app/middleware.php';
        require __DIR__ . '/../../src/app/routes.php';
        file_put_contents($settings['settings']['db']['database'], '');
        $config = new Config(require __DIR__ . '/../../src/Config/phinx.php');
        $manager = new Manager($config, new StringInput(' '), new NullOutput());
        $manager->migrate('local');
        $manager->seed('local');
        return $app;
    }

    protected function createRequest(
        string $method,
        string $path,
        array $headers = ['HTTP_ACCEPT' => 'application/json'],
        array $cookies = [],
        array $serverParams = ['REMOTE_ADDR' => '127.0.0.1']
    ) {
        $uri = new Uri('', '', 80, $path);
        $handle = fopen('php://temp', 'w+');
        $stream = (new StreamFactory())->createStreamFromResource($handle);
        $h = new Headers();
        foreach ($headers as $name => $value) {
            $h->addHeader($name, $value);
        }
        return new SlimRequest($method, $uri, $h, $cookies, $serverParams, $stream);
    }
}
