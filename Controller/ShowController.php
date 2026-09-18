<?php

namespace Controller;

use Model\Show;

class ShowController
{
    private Show $showModel;

    public function __construct()
    {
        $this->showModel = new Show();
    }

    public function validate(string $nome, string $local, string $dataShow, int $capacidade, float $precoIngresso): ?string
    {
        if ($nome === '' || $local === '' || $dataShow === '') {
            return 'Preencha nome, local e data do show.';
        }

        if ($capacidade <= 0) {
            return 'A capacidade deve ser maior que zero.';
        }

        if ($precoIngresso < 0) {
            return 'O valor do ingresso não pode ser negativo.';
        }

        $timestamp = strtotime($dataShow);
        if ($timestamp === false) {
            return 'Data do show inválida.';
        }

        if ($timestamp < time()) {
            return 'A data do show não pode estar no passado.';
        }

        return null;
    }

    public function create(string $nome, string $artista, string $local, string $dataShow, int $capacidade, float $precoIngresso, int $idUsuario): array|false 
        {
        return $this->showModel->create( $nome,  $artista, $local,  $dataShow,  $capacidade, $precoIngresso, $idUsuario);
    }

    public function listAll(int $idUsuario): array|false
    {
        return $this->showModel->getAll($idUsuario);
    }

    public function find(int $id, int $idUsuario): array|false
    {
        return $this->showModel->getById($id, $idUsuario);
    }

    public function update(int $id, string $nome, string $artista, string $local, string $dataShow, int $capacidade, float $precoIngresso, int $idUsuario): array|false 
    {
        return $this->showModel->update($id, $nome, $artista, $local, $dataShow, $capacidade, $precoIngresso, $idUsuario);
    }

    public function delete(int $id, int $idUsuario): bool
    {
        return $this->showModel->delete($id, $idUsuario);
    }
}
