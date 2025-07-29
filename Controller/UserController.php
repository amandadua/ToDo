<?php
namespace Controller;

use PDO;

class UserController {
    private $pdo;

    public function __construct() {
        $this->pdo = new PDO('mysql:host=localhost;dbname=todo', 'root', '');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function login($email, $password) {
        $stmt = $this->pdo->prepare("SELECT * FROM user WHERE user_email = :email");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            if (password_verify($password, $user['password'])) {
                return true;
            } else {
                // Senha incorreta
                return false;
            }
        }

        return false;
    }
}
?>