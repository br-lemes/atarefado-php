<?php

namespace Tests\Utils;

use Slim\Psr7\Factory\StreamFactory;
use Slim\Psr7\Headers;
use Slim\Psr7\Request;
use Slim\Psr7\Uri;

class WebTestClient
{
    /** @var \Slim\App */
    public $app;

    /** @var \Slim\Http\Request */
    public $request;

    /** @var \Slim\Http\Response */
    public $response;

    private $cookies = [];
    private $tokenJwt;

    public function __construct($appSlim)
    {
        $this->app = $appSlim;
    }

    public function __call($method, $arguments)
    {
        throw new \BadMethodCallException(strtoupper($method) . ' is not supported');
    }

    public function get(string $path, $data = [], $optionalHeaders = [])
    {
        return $this->request('GET', $path, $data, $optionalHeaders);
    }

    public function post(string $path, $data = [], $optionalHeaders = [])
    {
        return $this->request('POST', $path, $data, $optionalHeaders);
    }

    public function patch(string $path, $data = [], $optionalHeaders = [])
    {
        return $this->request('PATCH', $path, $data, $optionalHeaders);
    }

    public function put(string $path, $data = [], $optionalHeaders = [])
    {
        return $this->request('PUT', $path, $data, $optionalHeaders);
    }

    public function delete(string $path, $data = [], $optionalHeaders = [])
    {
        return $this->request('DELETE', $path, $data, $optionalHeaders);
    }

    public function head(string $path, $data = [], $optionalHeaders = [])
    {
        return $this->request('head', $path, $data, $optionalHeaders);
    }

    public function options(string $path, $data = [], $optionalHeaders = [])
    {
        return $this->request('options', $path, $data, $optionalHeaders);
    }

    public function setJwt(string $tokenAccess)
    {
        $this->tokenJwt = $tokenAccess;
    }

    public function getBodyArray()
    {
        return json_decode($this->response->getBody(), true);
    }

    private function request($method, $path, $data = [], $optionalHeaders = [])
    {
        $method = strtoupper($method);
        $queryParams = '';
        if ($method === 'GET') {
            $queryParams = http_build_query($data);
        } else {
            $params = json_encode($data);
        }

        // phpunit fix #3026
        if (!isset($_SERVER['REQUEST_URI'])) {
            $_SERVER = [
                'SCRIPT_NAME' => '/public/index.php',
                'REQUEST_TIME_FLOAT' => microtime(true),
                'REQUEST_TIME' => (int) microtime(true),
            ];
        }

        $serverParams = ['REMOTE_ADDR' => '127.0.0.1', 'QUERY_STRING' => ['teste' => 'teste']];
        $uri = new Uri('', '', 80, $path, $queryParams);
        $handle = fopen('php://temp', 'w+');
        $stream = (new StreamFactory())->createStreamFromResource($handle);
        $headers = new Headers();
        $this->request = new Request($method, $uri, $headers, [], $serverParams, $stream);
        $this->request->withQueryParams(['teste' => 'teste']);
        if (isset($params)) {
            $this->request = $this->request->withParsedBody($data);
            $this->request = $this->request->withHeader('Content-Type', 'application/json');
        }
        foreach ($optionalHeaders as $key => $value) {
            $this->request = $this->request->withHeader($key, $value);
        }
        if ($this->tokenJwt) {
            $this->request = $this->request->withHeader('Authorization', "Bearer $this->tokenJwt");
        }
        $this->response = $this->app->handle($this->request);
        return $this->response->getBody();
    }

    public function setCookie($name, $value)
    {
        $this->cookies[$name] = $value;
    }
}
