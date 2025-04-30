<?php
// app/services/AuthService.php
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../utilities/PasswordHash.php';

class AuthService
{
    private $db;

    private $userModel;

    public function __construct($userModel)
    {
        $this->userModel = $userModel;
        $this->db = Database::getInstance()->getConnection();
    }

    public function login($email, $password)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email AND account_status = 'active'");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        $passwordHasher = new PasswordHash();
        if ($user && $passwordHasher->verify($password, $user['password'])) {
            // Set session
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['last_active'] = time();
            return ['success' => true];
        }

        return ['success' => false, 'message' => 'Invalid email or password'];
    }

    public static function logout()
    {
        session_start();
        session_unset();
        session_destroy();
        header("Location: login.php");
        exit;
    }

    public static function check()
    {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header("Location: login.php");
            exit;
        }

        // Session timeout
        require_once __DIR__ . '/../../config/Config.php';
        if (time() - $_SESSION['last_active'] > Config::sessionTimeout()) {
            self::logout();
        }

        $_SESSION['last_active'] = time(); // refresh activity
    }

    public static function requireRole($role)
    {
        self::check();
        if ($_SESSION['role'] !== $role) {
            header("Location: unauthorized.php"); // Optional unauthorized page
            exit;
        }
    }
}
