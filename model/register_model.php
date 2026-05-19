<?php
// model/register_model.php
require_once __DIR__ . '/../control/db_config.php';

// Queries table space via MySQLi parameterized bindings
function emailExists($email) {
    global $conn; // Uses the MySQLi connection object from db_config.php
    
    $sql = "SELECT id FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        // "s" means the parameter type is a String
        $stmt->bind_param("s", $email);
        $stmt->execute();
        
        // Store the result set to inspect row count
        $stmt->store_result();
        $rowCount = $stmt->num_rows;
        
        $stmt->close();
        return $rowCount > 0;
    }
    
    return false;
}

// Executes secure MySQLi parameter binding to insert user data
function registerUser($name, $email, $password, $role, $address, $phone) {
    global $conn;
    
    $sql = "INSERT INTO users (name, email, password, role, address, phone, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, NOW())";
    
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        // "ssssss" means 6 parameters, all of type String
        $stmt->bind_param("ssssss", $name, $email, $password, $role, $address, $phone);
        
        $result = $stmt->execute();
        $stmt->close();
        
        return $result; // Returns true on success, false on failure
    }
    
    return false;
}
?>