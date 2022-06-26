<?php

namespace Modulos\System\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Factory\ResponseFactory as SlimPsr7ResponseFactory;
use Selective\Config\Configuration;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;
use RuntimeException;
use DomainException;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Modulos\System\Models\Token;

class JwtAuth
{
    protected $model;
    protected $secret;
    protected $log;

    public function __construct(Configuration $config, LoggerInterface $log, Token $model)
    {
        $this->log = $log;
        $this->model = $model;
        $this->secret = $config->getString('settings.jwt.secret');
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        try {
            $token = $this->fetchToken($request);
            $decoded = $this->decodeToken($token);
            list('data' => $data) = $decoded;
            $dbToken = $this->model->find($data->tokenId);
            if (!$dbToken || $dbToken->logout_data || $dbToken->token !== $token) {
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
                $this->log->log(LogLevel::DEBUG, 'Usando token do cabeçalho');
                return $matches[1];
            }
        }
        $this->log->warning('Token não encontrado.');
        throw new RuntimeException('Token não encontrado.');
    }

    private function decodeToken(string $token): array
    {
        try {
            $decoded = JWT::decode($token, new Key($this->secret, 'HS256'));
            return (array) $decoded;
        } catch (Exception $ex) {
            $this->log->log(LogLevel::DEBUG, $ex->getMessage());
            throw $ex;
        }
    }
}
