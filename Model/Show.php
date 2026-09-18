<?php

namespace Model;

use PDO;
use PDOException;

class ShowModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Connection::getInstance();
    }

    public function create(string $nome, string $artista, string $local, string $dataShow, int $capacidade, float $precoIngresso, int $idUsuario): array|false
    {
        try {
            $sql = "INSERT INTO shows
                    (nome, artista, local, data_show, capacidade, preco_ingresso, criado_em, id_usuario)
                    VALUES (:nome, :artista, :local, :data_show, :capacidade, :preco_ingresso, NOW(), :id_usuario)
                    RETURNING *";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':nome' => $nome,
                ':artista' => $artista,
                ':local' => $local,
                ':data_show' => $dataShow,
                ':capacidade' => $capacidade,
                ':preco_ingresso' => $precoIngresso,
                ':id_usuario' => $idUsuario,
            ]);

            return $stmt->fetch(PDO::FETCH_ASSOC) ?: false;
        } catch (PDOException $error) {
            error_log('Erro ao criar show: ' . $error->getMessage());
            return false;
        }
    }

    public function getAll(int $idUsuario): array|false
    {
        try {
            $sql = "SELECT * FROM shows WHERE id_usuario = :id_usuario ORDER BY data_show ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id_usuario' => $idUsuario]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $error) {
            error_log('Erro ao listar shows: ' . $error->getMessage());
            return false;
        }
    }

    public function getById(int $id, int $idUsuario): array|false
    {
        try {
            $sql = "SELECT * FROM shows WHERE id = :id AND id_usuario = :id_usuario";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':id' => $id,
                ':id_usuario' => $idUsuario,
            ]);

            return $stmt->fetch(PDO::FETCH_ASSOC) ?: false;
        } catch (PDOException $error) {
            error_log('Erro ao buscar show: ' . $error->getMessage());
            return false;
        }
    }

    public function update(
        int $id,
        string $nome,
        string $artista,
        string $local,
        string $dataShow,
        int $capacidade,
        float $precoIngresso,
        int $idUsuario
    ): array|false {
        try {
            $sql = "UPDATE shows
                    SET nome = :nome,
                        artista = :artista,
                        local = :local,
                        data_show = :data_show,
                        capacidade = :capacidade,
                        preco_ingresso = :preco_ingresso
                    WHERE id = :id AND id_usuario = :id_usuario
                    RETURNING *";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':nome' => $nome,
                ':artista' => $artista,
                ':local' => $local,
                ':data_show' => $dataShow,
                ':capacidade' => $capacidade,
                ':preco_ingresso' => $precoIngresso,
                ':id' => $id,
                ':id_usuario' => $idUsuario,
            ]);

            return $stmt->fetch(PDO::FETCH_ASSOC) ?: false;
        } catch (PDOException $error) {
            error_log('Erro ao atualizar show: ' . $error->getMessage());
            return false;
        }
    }

    public function delete(int $id, int $idUsuario): bool
    {
        try {
            $sql = "DELETE FROM shows WHERE id = :id AND id_usuario = :id_usuario";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':id' => $id,
                ':id_usuario' => $idUsuario,
            ]);

            return $stmt->rowCount() > 0;
        } catch (PDOException $error) {
            error_log('Erro ao excluir show: ' . $error->getMessage());
            return false;
        }
    }
}
