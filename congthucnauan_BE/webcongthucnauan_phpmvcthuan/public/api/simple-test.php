<?php
header('Content-Type: application/json');

$basePath = dirname(dirname(__DIR__));
$configPath = $basePath . '/app/config/config.php';

$response = [
    'status' => 'test',
    'current_dir' => __DIR__,
    'base_path' => $basePath,
    'config_path' => $configPath,
    'config_exists' => file_exists($configPath),
    'files_in_config_dir' => is_dir($basePath . '/app/config') ? scandir($basePath . '/app/config') : 'Config dir not found'
];

echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); 