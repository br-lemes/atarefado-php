<?php

declare(strict_types=1);

namespace App\Controller;

use App\Lib\ResponseTrait;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class IndexController
{
    use ResponseTrait;

    public function index(Request $request, Response $response)
    {
        return $this->withJson(['message' => 'Hello, World!']);
    }
}
