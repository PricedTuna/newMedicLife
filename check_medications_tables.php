<?php
require_once 'config/database.config.php';

// Get database connection
$pdo = getConnection();

if ($pdo === null) {
    echo "Error: Could not connect to the database.\n";
    exit;
}

// Function to show table structure
function showTableStructure($pdo, $tableName) {
    echo "\nTable Structure for $tableName:\n";
    $stmt = $pdo->prepare("
        SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE, COLUMN_DEFAULT
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
        AND TABLE_NAME = :tableName
        ORDER BY ORDINAL_POSITION
    ");
    $stmt->execute([':tableName' => $tableName]);
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($columns) > 0) {
        echo "Column Name | Data Type | Nullable | Default\n";
        echo "-----------------------------------------------\n";
        foreach ($columns as $column) {
            echo "{$column['COLUMN_NAME']} | {$column['DATA_TYPE']} | {$column['IS_NULLABLE']} | " . 
                 (isset($column['COLUMN_DEFAULT']) ? $column['COLUMN_DEFAULT'] : 'NULL') . "\n";
        }
    } else {
        echo "No columns found for table $tableName\n";
    }
}

// Check if medications_types table exists
$stmt = $pdo->prepare("
    SELECT COUNT(*) as table_exists 
    FROM information_schema.tables 
    WHERE table_schema = DATABASE() 
    AND table_name = 'medications_types'
");
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if ($result['table_exists'] == 0) {
    echo "Error: The medications_types table does not exist.\n";
} else {
    echo "Success: The medications_types table exists.\n";

    // Show table structure
    showTableStructure($pdo, 'medications_types');

    // Check if description column exists
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as column_exists 
        FROM information_schema.columns 
        WHERE table_schema = DATABASE() 
        AND table_name = 'medications_types' 
        AND column_name = 'description'
    ");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $hasDescriptionColumn = $result['column_exists'] > 0;
    echo "\nDescription column exists: " . ($hasDescriptionColumn ? "Yes" : "No") . "\n";

    // Check if status column exists
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as column_exists 
        FROM information_schema.columns 
        WHERE table_schema = DATABASE() 
        AND table_name = 'medications_types' 
        AND column_name = 'status'
    ");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $hasStatusColumn = $result['column_exists'] > 0;
    echo "Status column exists: " . ($hasStatusColumn ? "Yes" : "No") . "\n";

    // Check if there are any medication types
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM medications_types");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    echo "\nThere are " . $result['count'] . " medication types in the database.\n";

    // List all medication types
    if ($result['count'] > 0) {
        $stmt = $pdo->prepare("SELECT * FROM medications_types");
        $stmt->execute();
        $types = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo "Medication Types:\n";
        foreach ($types as $type) {
            echo "ID: " . $type['id'] . ", Name: " . $type['name'];
            if ($hasDescriptionColumn) {
                echo ", Description: " . (isset($type['description']) ? $type['description'] : 'N/A');
            }
            if ($hasStatusColumn) {
                echo ", Status: " . (isset($type['status']) ? $type['status'] : 'N/A');
            } else {
                echo ", Status: N/A";
            }
            echo "\n";
        }
    }
}

// Check if medications table exists
$stmt = $pdo->prepare("
    SELECT COUNT(*) as table_exists 
    FROM information_schema.tables 
    WHERE table_schema = DATABASE() 
    AND table_name = 'medications'
");
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if ($result['table_exists'] == 0) {
    echo "Error: The medications table does not exist.\n";
} else {
    echo "\nSuccess: The medications table exists.\n";

    // Show table structure
    showTableStructure($pdo, 'medications');

    // Check if there are any medications
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM medications");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    echo "\nThere are " . $result['count'] . " medications in the database.\n";

    // List all medications
    if ($result['count'] > 0) {
        $stmt = $pdo->prepare("
            SELECT m.*, mt.name as type_name 
            FROM medications m
            JOIN medications_types mt ON m.id_medicine_type = mt.id
        ");
        $stmt->execute();
        $medications = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo "Medications:\n";
        foreach ($medications as $medication) {
            echo "ID: " . $medication['id'] . ", Name: " . $medication['name'] . ", Type: " . $medication['type_name'] . 
                 ", Stock: " . (isset($medication['stock']) ? $medication['stock'] : 'N/A') . 
                 ", Price Purchase: " . (isset($medication['price_purchase']) ? $medication['price_purchase'] : 'N/A') . 
                 ", Price Sale: " . (isset($medication['price_sale']) ? $medication['price_sale'] : 'N/A') . 
                 ", Status: " . (isset($medication['status']) ? $medication['status'] : 'N/A') . "\n";
        }
    }
}
?>
