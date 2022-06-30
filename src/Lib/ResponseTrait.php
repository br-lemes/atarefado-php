<?php

namespace App\Lib;

use Slim\Psr7\Factory\ResponseFactory;

trait ResponseTrait
{
    public function withJson($data, $code = 200)
    {
        $payload = json_encode($data);
        $response = (new ResponseFactory)->createResponse($code);
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}
