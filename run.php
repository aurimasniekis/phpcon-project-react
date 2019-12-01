<?php

use GuzzleHttp\Client;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Response;
use React\Http\Server;

require_once __DIR__ . '/vendor/autoload.php';

$loop = React\EventLoop\Factory::create();

$server = new Server(
    function (ServerRequestInterface $request) {
        $url    = 'https://gist.githubusercontent.com/aurimasniekis/437b0e4ee49eb54c9ce190534ea2ac36/raw/' .
            '824797624d5c516717cc65fef1de1ae937681ceb/currencies.json';
        $client = new Client();

        $response = $client->get($url);
        $json     = json_decode($response->getBody()->getContents(), true, JSON_THROW_ON_ERROR);

        return new Response(
            200,
            array(
                'Content-Type' => 'application/json',
            ),
            json_encode(
                [
                    'success' => true,
                    'data'    => $json['rates'],
                ]
            )
        );
    }
);

$socket = new React\Socket\Server(8080, $loop);
$server->listen($socket);

$loop->run();