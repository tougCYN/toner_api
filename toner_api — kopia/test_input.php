<?php
header("Content-Type: text/plain");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

echo "php://input:\n";
var_dump(file_get_contents("php://input"));

echo "\n\n\$_POST:\n";
var_dump($_POST);

echo "\n\n\$_SERVER['CONTENT_TYPE']:\n";
var_dump($_SERVER['CONTENT_TYPE'] ?? null);
