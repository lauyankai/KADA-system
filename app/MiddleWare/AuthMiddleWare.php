<?php
namespace App\Middleware;

class AuthMiddleware
{
    public static function identifyUserType($username)
    {
        if (preg_match('/^[\d-]+$/', $username)) {
            return 'member';
        }

        if (preg_match('/^DIR/i', $username)) {
            return 'director';
        }

        return 'admin';
    }

    public static function validateAccess($userType)
    {
        switch ($userType) {
            case 'member':
                if (!isset($_SESSION['member_id'])) {
                    $_SESSION['error'] = 'Sila log masuk untuk mengakses';
                    header('Location: /auth/login');
                    exit;
                }
                break;

            case 'admin':
                if (!isset($_SESSION['admin_id'])) {
                    $_SESSION['error'] = 'Sila log masuk sebagai admin';
                    header('Location: /auth/login');
                    exit;
                }
                break;

            case 'director':
                if (!isset($_SESSION['director_id'])) {
                    $_SESSION['error'] = 'Sila log masuk sebagai pengarah';
                    header('Location: /auth/login');
                    exit;
                }
                break;
        }
    }
}