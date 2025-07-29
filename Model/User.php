<?php

namespace Model;

require_once __DIR__ . '/Connection.php'; 

use Model\Connection;

use PDO;
use PDOException;
use Exception;

class User
{
    private $db;

    public function __construct()
    {
        $this->db = Connection::getInstance();
    }

    public function registerUser($user_fullname, $email, $password)
    {
        try {
            $sql = 'INSERT INTO user (user_name, user_email, password, created_at) VALUES (:user_name, :user_email, :password, NOW())';

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $this->db->prepare($sql);

            $stmt->bindParam(":user_name", $user_fullname, PDO::PARAM_STR);
            $stmt->bindParam(":user_email", $email, PDO::PARAM_STR);
            $stmt->bindParam(":password", $hashedPassword, PDO::PARAM_STR);

            return $stmt->execute();

        } catch (PDOException $error) {
            echo "Erro ao executar o comando " . $error->getMessage();
            return false;
        }
    }

    public function getUserByEmail($email)
    {
        try {
            $sql = "SELECT * FROM user WHERE email = :email LIMIT 1";

            $stmt = $this->db->prepare($sql);

            $stmt->bindParam(":email", $email, PDO::PARAM_STR);

            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $error) {
        }
    }

    public function getUserInfo($id, $user_fullname, $email)
    {
        try {
            $sql = "SELECT user_fullname, email FROM user WHERE id = :id AND user_fullname = :user_fullname AND email = :email";

            $stmt = $this->db->prepare($sql);

            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt->bindParam(":user_fullname", $user_fullname, PDO::PARAM_STR);
            $stmt->bindParam(":email", $email, PDO::PARAM_STR);

            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $error) {
            echo "Erro ao buscar informações: " . $error->getMessage();
            return false;
        }
    }
}

?>