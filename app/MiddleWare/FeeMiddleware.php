<?php
namespace App\Middleware;

class FeeMiddleware
{
    public static function checkInitialFees()
    {
        if (!isset($_SESSION['member_id'])) {
            return;
        }

        // Skip check for these routes
        $allowedRoutes = [
            '/users/fees/initial',
            '/users/fees/process',
            '/users/fees/success',
            '/auth/logout'
        ];

        $currentRoute = $_SERVER['REQUEST_URI'];
        if (in_array($currentRoute, $allowedRoutes)) {
            return;
        }

        // Check if member has pending fees
        $db = new \App\Core\Database();
        $stmt = $db->connect()->prepare(
            "SELECT payment_status FROM member_fees WHERE member_id = :member_id"
        );
        $stmt->execute([':member_id' => $_SESSION['member_id']]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$result || $result['payment_status'] === 'pending') {
            header('Location: /users/fees/initial');
            exit;
        }
    }
} 