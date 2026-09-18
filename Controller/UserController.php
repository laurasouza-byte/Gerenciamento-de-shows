<?php

namespace Controller;

use Model\User;

class UserController
{
    private User $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new User();
    }

    public function register(string $nome, string $email, string $senha): ?string
    {
        if ($nome === '' || $email === '' || $senha === '') {
            return 'Preencha todos os campos.';
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return 'E-mail inválido.';
        }

        if (strlen($senha) < 6) {
            return 'A senha deve ter pelo menos 6 caracteres.';
        }

        if ($this->usuarioModel->findByEmail($email)) {
            return 'Este e-mail já está cadastrado.';
        }

        if (!$this->usuarioModel->register($nome, $email, $senha)) {
            return 'Não foi possível realizar o cadastro.';
        }

        return null;
    }

    public function login(string $email, string $senha): bool
    {
        $usuario = $this->usuarioModel->findByEmail($email);

        if (!$usuario || !isset($usuario['senha']) || !password_verify($senha, $usuario['senha'])) {
            return false;
        }

        session_regenerate_id(true);

        $_SESSION['user_id'] = (int) $usuario['id'];
        $_SESSION['user_name'] = $usuario['nome'];

        return true;
    }

    public function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']);
    }

    public function currentUser(): ?array
    {
        if (!$this->isLoggedIn()) {
            return null;
        }

        return [
            'id' => (int) $_SESSION['user_id'],
            'nome' => $_SESSION['user_name']
        ];
    }

    public function logout(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
    }
}