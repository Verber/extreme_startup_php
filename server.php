<?php
require 'solver.php';
$host = "0.0.0.0";
$port = 9999;
set_time_limit(0);
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
$result = socket_bind($socket, $host, $port);
$result = socket_listen($socket, SOMAXCONN);
while (true) {
    $spawn = socket_accept($socket);
    $input = socket_read($spawn, 1024, PHP_NORMAL_READ);
    $input = trim($input);
    list($headers, $request) = explode('=', $input);
    list($id, $question) = explode(':', $request, 2);
    $question = substr($question, 0, -9);
    $question = urldecode($question);

    $answer = solve($question);

    $result = "HTTP/1.0 200 OK\n";
    $result .= "Date: Sat, 22 Dec 2012 00:00:01 GMT\n";
    $result .= "Content-Type: text/html\n";
    $result .= "Content-Length: " . strlen($answer) . "\n";
    $result .= "\n";
    $result .= $answer;

    $written = socket_write($spawn, $result, strlen($result));

    echo "Request: $input\n";
    echo "id: $id, question: $question\n";
    echo "Answer: $answer\n";
}
