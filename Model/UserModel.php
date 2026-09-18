<?php

require_once __DIR__ . '/../Config/configuration.php';

class UserModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = getConnection();
    }

    public function register(string $name, string $email, string $password): bool
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (name, email, password, created_at) VALUES (:name, :email, :password, NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashedPassword);

        return $stmt->execute();
    }

    public function findByEmail(string $email): array|false
    {
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
