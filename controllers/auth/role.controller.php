<?php
/**
 * Role-based access control controller
 * This controller provides functions to check if a user has the necessary role to access a specific feature
 * Roles:
 * - A: Administrator
 * - D: Doctor
 * - S: Secretary
 */

/**
 * Check if the current user has the required role
 * @param string|array $requiredRoles Single role or array of roles that are allowed
 * @param bool $redirect Whether to redirect to dashboard if user doesn't have the required role
 * @return bool True if user has the required role, false otherwise
 */
function checkUserRole($requiredRoles, $redirect = true) {
    // Make sure session is started
    if (session_status() == PHP_SESSION_NONE) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Check if user is logged in
    if (!isset($_SESSION['usuario']) || !isset($_SESSION['role'])) {
        if ($redirect) {
            header('Location: /index.php');
            exit();
        }
        return false;
    }

    // Convert single role to array for consistent handling
    if (!is_array($requiredRoles)) {
        $requiredRoles = [$requiredRoles];
    }

    // Check if user has one of the required roles
    if (in_array($_SESSION['role'], $requiredRoles)) {
        return true;
    }

    // User doesn't have the required role
    if ($redirect) {
        // Redirect to dashboard with access denied message
        header('Location: /views/dashboard/dashboard.view.php?error=' . urlencode("Acceso denegado. No tienes permisos para esta acción."));
        exit();
    }

    return false;
}

/**
 * Check if the current user is an administrator
 * @param bool $redirect Whether to redirect to dashboard if user is not an administrator
 * @return bool True if user is an administrator, false otherwise
 */
function isAdmin($redirect = true) {
    return checkUserRole('A', $redirect);
}

/**
 * Check if the current user is a doctor
 * @param bool $redirect Whether to redirect to dashboard if user is not a doctor
 * @return bool True if user is a doctor, false otherwise
 */
function isDoctor($redirect = true) {
    return checkUserRole('D', $redirect);
}

/**
 * Check if the current user is a secretary
 * @param bool $redirect Whether to redirect to dashboard if user is not a secretary
 * @return bool True if user is a secretary, false otherwise
 */
function isSecretary($redirect = true) {
    return checkUserRole('S', $redirect);
}
?>
