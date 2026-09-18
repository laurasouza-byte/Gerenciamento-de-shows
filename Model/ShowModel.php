<?php

require_once __DIR__ . '/../Config/configuration.php';


class ShowModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = getConnection();
    }

    public function create(string $name, string $artista, string $venue, string $date, int $capacity, float $price, int $userId): bool
    {
        $sql = "INSERT INTO shows (name, artista, venue, show_date, capacity, ticket_price, created_at, id_user) VALUES (:name, :artista, :venue, :show_date, :capacity, :ticket_price, NOW(), :id_user)";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':artista', $artista);
        $stmt->bindParam(':venue', $venue);
        $stmt->bindParam(':show_date', $date);
        $stmt->bindParam(':capacity', $capacity, PDO::PARAM_INT);
        $stmt->bindParam(':ticket_price', $price);
        $stmt->bindParam(':id_user', $userId, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function getAll(): array
    {
        $sql = "SELECT * FROM shows ORDER BY show_date ASC";
        $stmt = $this->db->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM shows WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }
}
