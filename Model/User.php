<?php

namespace Model;

use PDO;
use PDOException;

class User
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Connection::getInstance();
    }

    public function register(string $nome, string $email, string $senha): bool
    {
        try {
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

            $sql = "INSERT INTO users (nome, email, senha, criado_em) VALUES (:nome, :email, :senha, NOW())";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':senha', $senhaHash, PDO::PARAM_STR);

            return $stmt->execute();
        } catch (PDOException $error) {
            error_log('Erro ao registrar usuário: ' . $error->getMessage());
            return false;
        }
    }

    public function findByEmail(string $email): array|false
    {
        try {
            $sql = "SELECT * FROM users WHERE email = :email";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $error) {
            error_log('Erro ao buscar usuário: ' . $error->getMessage());
            return false;
        }
    }

    public function findById(int $id): array|false
    {
        try {
            $sql = "SELECT id, nome, email, criado_em FROM users WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $error) {
            error_log('Erro ao buscar usuário: ' . $error->getMessage());
            return false;
        }
    }
}
