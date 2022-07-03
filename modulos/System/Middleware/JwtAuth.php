<?php

namespace Modulos\System\Middleware;

use DomainException;
use Modulos\System\Service\TokenJwt;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use RuntimeException;
use Slim\Psr7\Factory\ResponseFactory as SlimPsr7ResponseFactory;

class JwtAuth
{
    protected $jwt;
    protected $log;

    public function __construct(LoggerInterface $log, TokenJwt $jwt)
    {
        $this->log = $log;
        $this->jwt = $jwt;
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        try {
            $token = $this->fetchToken($request);
            $data = $this->jwt->getValid($token);
            if (!$data) {
                $this->log->warning('Token inválido.');
                throw new RuntimeException('Token inválido.');
            }
        } catch (RuntimeException | DomainException $ex) {
            $response = (new SlimPsr7ResponseFactory)->createResponse(401, '');
            return $response;
        }
        $request = $request->withAttribute('token', $token)
            ->withAttribute('usuario', $data);
        $response = $handler->handle($request);
        return $response;
    }

    private function fetchToken(Request $request): string
    {
        $header = $request->getHeaderLine('Authorization');
        if (!empty($header)) {
            if (preg_match('/Bearer\s+(.*)$/i', $header, $matches)) {
                $this->log->log(LogLevel::DEBUG, 'Usando token do cabeçalho.');
                return $matches[1];
            }
        }
        $this->log->warning('Token não encontrado.');
        throw new RuntimeException('Token não encontrado.');
    }
}
