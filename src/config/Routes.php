<?php

$url = 'http://localhost:8080/';
$endpoint = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$request = str_replace($url, '', $endpoint);
$request = explode('/', strtolower($request));
$request['method'] = $_SERVER['REQUEST_METHOD'];

if ($_SERVER['HTTP_TOKEN']) {
    $request['token'] = $_SERVER['HTTP_TOKEN'];
}