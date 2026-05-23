<?php
/**
 * Debugging helper functions
 */

// Log debugging information to a file
function debug_to_file($message, $data = null) {
    $log_file = APP_ROOT . '/logs/debug.log';
    
    // Create logs directory if it doesn't exist
    if (!is_dir(dirname($log_file))) {
        mkdir(dirname($log_file), 0777, true);
    }
    
    $timestamp = date('Y-m-d H:i:s');
    $log_message = "[$timestamp] $message";
    
    if ($data !== null) {
        $log_message .= "\n" . print_r($data, true);
    }
    
    $log_message .= "\n" . str_repeat('-', 50) . "\n";
    
    // Append to log file
    file_put_contents($log_file, $log_message, FILE_APPEND);
}

// Debug database queries
function db_debug($query, $params = [], $error = null) {
    $message = "SQL Query: $query\n";
    $message .= "Parameters: " . print_r($params, true);
    
    if ($error !== null) {
        $message .= "\nError: $error";
    }
    
    debug_to_file($message);
}

// Test database connection and log results
function test_db_connection() {
    try {
        $db_host = DB_HOST;
        $db_name = DB_NAME;
        $db_user = DB_USER;
        $db_pass = DB_PASS;
        
        $dsn = "mysql:host={$db_host};dbname={$db_name};charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false
        ];
        
        $pdo = new PDO($dsn, $db_user, $db_pass, $options);
        debug_to_file("Database connection successful", [
            'host' => $db_host,
            'database' => $db_name
        ]);
        
        // Check if contacts table exists
        $stmt = $pdo->query("SHOW TABLES LIKE 'contacts'");
        $table_exists = ($stmt->rowCount() > 0);
        debug_to_file("Contacts table exists: " . ($table_exists ? 'Yes' : 'No'));
        
        if ($table_exists) {
            // Check table structure
            $stmt = $pdo->query("DESCRIBE contacts");
            $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
            debug_to_file("Contacts table structure:", $columns);
        }
        
        return true;
    } catch (PDOException $e) {
        debug_to_file("Database connection failed: " . $e->getMessage(), [
            'host' => $db_host,
            'database' => $db_name
        ]);
        return false;
    }
}
?>
