<?php
header('Access-Control-Allow-Origin: *');
header("Content-type: application/json; charset=utf-8");

error_reporting(E_ALL & ~E_NOTICE);
session_start();