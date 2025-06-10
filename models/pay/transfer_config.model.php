<?php
/**
 * Transfer Configuration Model
 * This model handles the bank transfer configuration data
 */

class TransferConfigModel {
    private $pdo;

    /**
     * Constructor
     * @param PDO $pdo PDO connection object
     */
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->createTableIfNotExists();
    }

    /**
     * Create the transfer_config table if it doesn't exist
     */
    private function createTableIfNotExists() {
        $sql = "CREATE TABLE IF NOT EXISTS transfer_config (
            id INT AUTO_INCREMENT PRIMARY KEY,
            bank_name VARCHAR(100) NOT NULL,
            account_holder VARCHAR(100) NOT NULL,
            account_number VARCHAR(50) NOT NULL,
            clabe VARCHAR(50) NOT NULL,
            additional_info TEXT,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            updated_by INT,
            FOREIGN KEY (updated_by) REFERENCES users(id)
        )";

        try {
            $this->pdo->exec($sql);
            
            // Check if table is empty, insert default values if it is
            $stmt = $this->pdo->query("SELECT COUNT(*) FROM transfer_config");
            $count = $stmt->fetchColumn();
            
            if ($count == 0) {
                $this->insertDefaultConfig();
            }
        } catch (PDOException $e) {
            error_log("Error creating transfer_config table: " . $e->getMessage());
        }
    }

    /**
     * Insert default configuration values
     */
    private function insertDefaultConfig() {
        $sql = "INSERT INTO transfer_config 
                (bank_name, account_holder, account_number, clabe, additional_info) 
                VALUES 
                ('BBVA', 'Medic Life S.A. de C.V.', '0123456789', '012345678901234567', 'Información adicional para transferencias bancarias')";
        
        try {
            $this->pdo->exec($sql);
        } catch (PDOException $e) {
            error_log("Error inserting default transfer config: " . $e->getMessage());
        }
    }

    /**
     * Get the current transfer configuration
     * @return array|null Configuration data or null if not found
     */
    public function getConfig() {
        try {
            $stmt = $this->pdo->query("SELECT * FROM transfer_config ORDER BY id DESC LIMIT 1");
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting transfer config: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Update the transfer configuration
     * @param array $data Configuration data
     * @param int $userId ID of the user making the update
     * @return bool True if successful, false otherwise
     */
    public function updateConfig($data, $userId) {
        try {
            $sql = "UPDATE transfer_config SET 
                    bank_name = :bank_name,
                    account_holder = :account_holder,
                    account_number = :account_number,
                    clabe = :clabe,
                    additional_info = :additional_info,
                    updated_by = :updated_by
                    WHERE id = :id";
            
            $stmt = $this->pdo->prepare($sql);
            
            return $stmt->execute([
                ':bank_name' => $data['bank_name'],
                ':account_holder' => $data['account_holder'],
                ':account_number' => $data['account_number'],
                ':clabe' => $data['clabe'],
                ':additional_info' => $data['additional_info'],
                ':updated_by' => $userId,
                ':id' => $data['id']
            ]);
        } catch (PDOException $e) {
            error_log("Error updating transfer config: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Create a new transfer configuration
     * @param array $data Configuration data
     * @param int $userId ID of the user creating the config
     * @return bool True if successful, false otherwise
     */
    public function createConfig($data, $userId) {
        try {
            $sql = "INSERT INTO transfer_config 
                    (bank_name, account_holder, account_number, clabe, additional_info, updated_by) 
                    VALUES 
                    (:bank_name, :account_holder, :account_number, :clabe, :additional_info, :updated_by)";
            
            $stmt = $this->pdo->prepare($sql);
            
            return $stmt->execute([
                ':bank_name' => $data['bank_name'],
                ':account_holder' => $data['account_holder'],
                ':account_number' => $data['account_number'],
                ':clabe' => $data['clabe'],
                ':additional_info' => $data['additional_info'],
                ':updated_by' => $userId
            ]);
        } catch (PDOException $e) {
            error_log("Error creating transfer config: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Save the transfer configuration (update if exists, create if not)
     * @param array $data Configuration data
     * @param int $userId ID of the user saving the config
     * @return bool True if successful, false otherwise
     */
    public function saveConfig($data, $userId) {
        // Check if config exists
        $config = $this->getConfig();
        
        if ($config) {
            // Update existing config
            $data['id'] = $config['id'];
            return $this->updateConfig($data, $userId);
        } else {
            // Create new config
            return $this->createConfig($data, $userId);
        }
    }
}
?>