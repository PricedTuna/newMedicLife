<?php
require_once 'config/database.config.php';

// Get database connection
$pdo = getConnection();

// Check if connection was successful
if ($pdo === null) {
    die("Failed to connect to the database.");
}

// Get the structure of the appointments table
try {
    $stmt = $pdo->prepare("DESCRIBE appointments");
    $stmt->execute();
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h2>Appointments Table Structure</h2>";
    echo "<table border='1'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td>" . $column['Field'] . "</td>";
        echo "<td>" . $column['Type'] . "</td>";
        echo "<td>" . $column['Null'] . "</td>";
        echo "<td>" . $column['Key'] . "</td>";
        echo "<td>" . $column['Default'] . "</td>";
        echo "<td>" . $column['Extra'] . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
    // Get a sample of data from the appointments table
    $stmt = $pdo->prepare("SELECT * FROM appointments LIMIT 5");
    $stmt->execute();
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($appointments) > 0) {
        echo "<h2>Sample Appointments Data</h2>";
        echo "<table border='1'>";
        
        // Table header
        echo "<tr>";
        foreach (array_keys($appointments[0]) as $key) {
            echo "<th>" . $key . "</th>";
        }
        echo "</tr>";
        
        // Table data
        foreach ($appointments as $appointment) {
            echo "<tr>";
            foreach ($appointment as $value) {
                echo "<td>" . $value . "</td>";
            }
            echo "</tr>";
        }
        
        echo "</table>";
    } else {
        echo "<p>No appointments found in the database.</p>";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>