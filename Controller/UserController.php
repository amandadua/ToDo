<?php
namespace Controller;

use PDO;



class UserController {
    public $conn;
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

    public function checkUserByEmail($email) {
    $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM user WHERE user_email = :email");
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();
    $count = $stmt->fetchColumn();
    return $count > 0;
    }


 public function createUser($user_fullname, $email, $password) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare("INSERT INTO user (user_fullname, user_email, password) VALUES (:user_fullname, :email, :password)");
        $stmt->bindParam(':user_fullname', $user_fullname, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
        return $stmt->execute();
    } // <-- aqui termina o método, não a classe!

    public function deletarConta($email) {
        $stmt = $this->pdo->prepare("DELETE FROM user WHERE user_email = ?");
        $stmt->execute([$email]);
    }

    public function alterarSenha($email, $novaSenha) {
        $hash = password_hash($novaSenha, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare("UPDATE user SET password = :senha WHERE user_email = :email");
        $stmt->bindParam(':senha', $hash, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
    }
} // <-- só UM fechamento da classe aqui!



?>