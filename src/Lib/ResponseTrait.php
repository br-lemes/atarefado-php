<?php

namespace App\Lib;

use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\StreamFactory;

trait ResponseTrait
{
    public function withJson($data, $code = 200)
    {
        $payload = json_encode($data);
        $response = (new ResponseFactory)->createResponse($code);
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function withValidation($msg, $code = 400)
    {
        $payload = json_encode([
            'msg' => $msg,
            'errorCode' => $code
        ]);
        $response = (new ResponseFactory)->createResponse($code);
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function responseFile($file, $customName = null, $forceDownload = false)
    {
        $stream = (new StreamFactory)->createStreamFromFile($file);
        $response = (new ResponseFactory)->createResponse(200);
        $response = $response
            ->withHeader('Content-Type', 'application/octet-stream')
            ->withHeader('Content-Type', 'application/download')
            ->withHeader('Content-Description', 'File Transfer')
            ->withHeader('Content-Transfer-Encoding', 'binary')
            ->withHeader(
                'Content-Disposition',
                'attachment; filename="' . ($customName ? $customName : basename($file)) . '"'
            )
            ->withHeader('Expires', '0')
            ->withHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0')
            ->withHeader('Pragma', 'public')
            ->withBody($stream);
        if ($forceDownload) {
            $response = $response->withHeader('Content-Type', 'application/force-download');
        }
        return $response;
    }
}
