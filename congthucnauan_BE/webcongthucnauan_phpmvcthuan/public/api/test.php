<?php
header('Content-Type: application/json');

// Load API classes
require_once __DIR__ . '/helpers/ApiLoader.php';

// Debug info
$debug = [
    'REQUEST_METHOD' => $_SERVER['REQUEST_METHOD'],
    'REQUEST_URI' => $_SERVER['REQUEST_URI'],
    'SCRIPT_NAME' => $_SERVER['SCRIPT_NAME'],
    'PATH_INFO' => $_SERVER['PATH_INFO'] ?? 'Not set',
    'QUERY_STRING' => $_SERVER['QUERY_STRING'] ?? 'Empty',
    'classes' => ApiLoader::checkClasses()
];

// Test response
$response = [
    'status' => 'success',
    'message' => 'API test successful',
    'debug' => $debug,
    'timestamp' => date('Y-m-d H:i:s')
];

echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?> 