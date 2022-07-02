<?php

require __DIR__ . '/../vendor/autoload.php';

use Slim\Psr7\Cookies;
use Slim\Psr7\Factory\StreamFactory;
use Slim\Psr7\Factory\UriFactory;
use Slim\Psr7\Headers;
use Slim\Psr7\Request;
use Slim\Psr7\Stream;
use Slim\Psr7\UploadedFile;

error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);

error_reporting(-1);

// setlocale(LC_ALL, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
date_default_timezone_set('America/Cuiaba');

require __DIR__ . '/../src/app/app.php';

$method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';
$uri = (new UriFactory())->createFromGlobals($_SERVER);
$headers = Headers::createFromGlobals();
$cookies = Cookies::parseHeader($headers->getHeader('Cookie', []));
$cacheResource = fopen('php://temp', 'wb+');
$cache = $cacheResource ? new Stream($cacheResource) : null;
$body = (new StreamFactory())->createStreamFromFile('php://input', 'r', $cache);
$uploadedFiles = UploadedFile::createFromGlobals($_SERVER);
$request = new Request($method, $uri, $headers, $cookies, $_SERVER, $body, $uploadedFiles);
$contentTypes = $request->getHeader('Content-Type') ?? [];
$parsedContentType = '';
foreach ($contentTypes as $contentType) {
    $fragments = explode(';', $contentType);
    $parsedContentType = current($fragments);
}
$contentTypesWithParsedBodies = ['application/x-www-form-urlencoded', 'multipart/form-data'];
if ($method === 'POST' && in_array($parsedContentType, $contentTypesWithParsedBodies)) {
    return $request->withParsedBody($_POST);
}

$app->run($request);
