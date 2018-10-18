<?php

use EventLoop\EventLoop;
use Rx\Scheduler\EventLoopScheduler;

require __DIR__ . "/vendor/autoload.php";

$loop = EventLoop::getLoop();
$scheduler = new EventLoopScheduler($loop);

startServer();

$ip = getIp();

echo "Waiting for other player ..." . PHP_EOL;

$client = new \Rx\Websocket\Client('ws://' . $ip);
$client
    ->retry()
    ->subscribe(
    function (\Rx\Websocket\MessageSubject $ms) {
        echo "Your friend is here !" . PHP_EOL;

        $ms->subscribe(
            function ($message) {
                echo $message . "\n";
            }
        );

        defineShip(5);
        defineShip(4);
        defineShip(3);
        defineShip(3);
        defineShip(2);

        //$ms->onNext('Hello');
    },
    function ($error) {
        echo "Could not connect" . PHP_EOL;
    },
    function () {
        echo "ended connection" . PHP_EOL;
    }
);

/* FUNCTIONS */
function getIp()
{
    GET_IP:
    $ip = readline("Please enter other player address [ip:port] : ");
    $check = explode(':', $ip);
    if (count($check) < 2) goto GET_IP;
    if (!filter_var($check[0], FILTER_VALIDATE_IP)) goto GET_IP;
    if (!filter_var($check[1], FILTER_VALIDATE_INT)) goto GET_IP;

    return $ip;
}

function startServer() {
    GET_PORT:
    $port = readline("Which port would you like to open to your friend : ");
    if (!filter_var($port, FILTER_VALIDATE_INT)) goto GET_PORT;

    $server = new \Rx\Websocket\Server('127.0.0.1:' . $port);

    $server->subscribe(function (\Rx\Websocket\MessageSubject $cs) {
        $cs->subscribe($cs);
    });
}

function defineShip($length)
{
    // TODO
}