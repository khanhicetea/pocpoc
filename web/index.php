<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';

$dotenv = new Dotenv\Dotenv(dirname(__DIR__));
$dotenv->load();

function getFullDbUrl() {
    return sprintf('%s/%s', getenv('DB_URL'), getenv('DB_NAME'));
}

function createNewNotification($type, $message) {
    if (empty(trim($message))) {
        return;
    }

    $types = [
        'w' => 'warning',
        'e' => 'error',
        's' => 'success',
        'i' => 'info',
    ];

    $notification_type = $types[strtolower($type)] ?? 'default';
    $sent_at = date('d/m/Y H:i:s');
    $client = new \GuzzleHttp\Client();
    $res = $client->request('POST', getFullDbUrl(), [
        'auth' => [getenv('DB_USER'), getenv('DB_PASS')],
        'json' => [
            '_id' => (string) time(),
            'type' => $notification_type,
            'sent_at' => $sent_at,
            'message' => $message
        ]
    ]);
}

date_default_timezone_set(getenv('TIMEZONE'));
$app = new \Slim\App;

$app->get('/_{secret}', function (Request $request, Response $response) {
    $secret = $request->getAttribute('secret');

    if ($secret != getenv('SECRET')) {
        return $response->withStatus(403);
    }
    
    $template = file_get_contents(__DIR__.'/../templates/index.html');
    $response->getBody()->write(str_replace('DB_FULL_URL', getFullDbUrl(), $template));
    return $response;
});

$app->get('/{type}/{message}', function(Request $request, Response $response) {
    $type = $request->getAttribute('type');
    $message = str_replace('-', ' ', $request->getAttribute('message'));
    createNewNotification($type, $message);

    return $response->withStatus(200);
});

$app->post('/{type}', function(Request $request, Response $response) {
    $type = $request->getAttribute('type');
    $data = $request->getParsedBody();
    $message = $data['message'] ?? null; 
    createNewNotification($type, $message);

    return $response->withStatus(200);
});

$app->run();
