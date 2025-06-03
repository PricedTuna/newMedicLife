<?php
// Script to create medical history tables

// Include database configuration
require_once 'config/database.config.php';

// Get database connection
$pdo = getConnection();

// Check if connection was successful
if ($pdo === null) {
    die("Error: Could not connect to the database. Please check your database configuration.");
}

// Read SQL from file
$sql = file_get_contents('models/medical_history/medical-history-tables.sql');

// Split SQL into individual statements
$statements = explode(';', $sql);

// Execute each statement
$success = true;
$errors = [];

foreach ($statements as $statement) {
    $statement = trim($statement);
    if (!empty($statement)) {
        try {
            $result = $pdo->exec($statement);
            if ($result === false) {
                $success = false;
                $errors[] = "Error executing statement: " . $statement . " - " . implode(', ', $pdo->errorInfo());
            }
        } catch (PDOException $e) {
            $success = false;
            $errors[] = "Exception: " . $e->getMessage() . " - Statement: " . $statement;
        }
    }
}

// Output result
if ($success) {
    echo "Success! All medical history tables have been created successfully.";
} else {
    echo "Error creating tables:<br>";
    foreach ($errors as $error) {
        echo $error . "<br>";
    }
}
?>