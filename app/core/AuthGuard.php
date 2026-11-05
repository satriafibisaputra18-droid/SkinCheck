<?php
/**
 * SkinCheck - Authentication and Authorization Guard Middleware
 * Enforces route protections, role verification, and session checks.
 */

class AuthGuard {
    /**
     * Prevent guest access. If user is not logged in, redirect to login page.
     */
    public static function enforceLoggedIn() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . URLROOT . '/auth/login');
            exit;
        }
    }

    /**
     * Enforce role-based access controls.
     * @param string|array $allowedRoles Active roles authorized to view page
     */
    public static function enforceRole($allowedRoles) {
        // First ensure user is logged in
        self::enforceLoggedIn();

        $userRole = $_SESSION['user_role'] ?? '';
        $roles = is_array($allowedRoles) ? $allowedRoles : [$allowedRoles];

        // 1. Role match: Grant access directly
        if (in_array($userRole, $roles)) {
            return;
        }

        // 2. Specific case: Admin attempting to access Customer pages
        // "Admin tidak boleh mengakses halaman yang hanya diperuntukkan untuk Customer jika tidak memiliki permission."
        if (in_array('customer', $roles) && $userRole === 'admin') {
            // Check if Admin has the customer access permission flag
            if (isset($_SESSION['has_customer_permission']) && $_SESSION['has_customer_permission'] === true) {
                return;
            }
        }

        // 3. Fallback: Role is unauthorized. Redirect to respective dashboard to prevent routing loops.
        if ($userRole === 'admin') {
            header('Location: ' . URLROOT . '/admin/dashboard');
        } else {
            header('Location: ' . URLROOT . '/customer/dashboard');
        }
        exit;
    }
}
