<?php
require_once 'config/database.config.php';

// Get database connection
$pdo = getConnection();

if ($pdo === null) {
    echo "Error: Could not connect to the database.\n";
    exit;
}

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

// Add the description column if it doesn't exist
if (!$hasDescriptionColumn) {
    try {
        $sql = "ALTER TABLE `medications_types` ADD COLUMN `description` text";
        $pdo->exec($sql);
        echo "Success: Added description column to medications_types table.\n";
    } catch (PDOException $e) {
        echo "Error: Failed to add description column: " . $e->getMessage() . "\n";
    }
} else {
    echo "Description column already exists in medications_types table.\n";
}

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

// Add the status column if it doesn't exist
if (!$hasStatusColumn) {
    try {
        $sql = "ALTER TABLE `medications_types` ADD COLUMN `status` char(1) NOT NULL DEFAULT 'A'";
        $pdo->exec($sql);
        echo "Success: Added status column to medications_types table.\n";
    } catch (PDOException $e) {
        echo "Error: Failed to add status column: " . $e->getMessage() . "\n";
    }
} else {
    echo "Status column already exists in medications_types table.\n";
}

// Update existing records to have status 'A'
try {
    $sql = "UPDATE `medications_types` SET `status` = 'A' WHERE `status` IS NULL";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $rowCount = $stmt->rowCount();
    echo "Updated $rowCount records to have status 'A'.\n";
} catch (PDOException $e) {
    echo "Error: Failed to update status values: " . $e->getMessage() . "\n";
}

echo "Done.\n";