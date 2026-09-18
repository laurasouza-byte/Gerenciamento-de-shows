<?php

require_once __DIR__ . '/../Model/ShowModel.php';

class ShowController
{
    private ShowModel $showModel;

    public function __construct()
    {
        $this->showModel = new ShowModel();
    }

    public function validate(string $name, string $venue, string $date, int $capacity, float $price): ?string
    {
        if (empty($name) || empty($venue) || empty($date)) {
            return "Preencha nome, local e data do show.";
        }

        if ($capacity <= 0) {
            return "A capacidade deve ser maior que zero.";
        }

        if ($price < 0) {
            return "O valor do ingresso não pode ser negativo.";
        }

        if (strtotime($date) < time()) {
            return "A data do show não pode estar no passado.";
        }

        return null;
    }

    public function create(string $name, string $artista, string $venue, string $date, int $capacity, float $price, int $userId): bool
    {
        return $this->showModel->create($name, $artista, $venue, $date, $capacity, $price, $userId);
    }

    public function listAll(): array
    {
        return $this->showModel->getAll();
    }

    public function delete(int $id): bool
    {
        return $this->showModel->delete($id);
    }
}
