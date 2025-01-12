<?php
namespace App\Middleware;

class AuthMiddleware
{
    public static function isAdmin()
    {
        if (!isset($_SESSION['is_admin'])  !$_SESSION['is_admin']) {
            $_SESSION['error'] = 'Akses tidak dibenarkan';
            header('Location: /auth/login');
            exit;
        }
    }

    public static function isMember()
    {
        if (!isset($_SESSION['is_member'])  !$_SESSION['is_member']) {
            $_SESSION['error'] = 'Sila log masuk untuk mengakses';
            header('Location: /auth/login');
            exit;
        }
    }
}