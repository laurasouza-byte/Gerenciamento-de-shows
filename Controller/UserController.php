<?php
require_once __DIR__ . '/../Model/UserModel.php';

/**
 * Regras de negócio relacionadas ao usuário: cadastro, login e sessão.
 */
class UserController
{
    private UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * Cadastra um novo usuário.
     * @return string|null Mensagem de erro, ou null se deu tudo certo.
     */
    public function register(string $name, string $email, string $password): ?string
    {
        if (empty($name) || empty($email) || empty($password)) {
            return "Preencha todos os campos.";
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "E-mail inválido.";
        }

        if (strlen($password) < 6) {
            return "A senha deve ter pelo menos 6 caracteres.";
        }

        if ($this->userModel->findByEmail($email)) {
            return "Este e-mail já está cadastrado.";
        }

        $this->userModel->register($name, $email, $password);
        return null;
    }

    public function login(string $email, string $password): bool
    {
        $user = $this->userModel->findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            return false;
        }

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];

        return true;
    }

    public function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']);
    }

    public function logout(): void
    {
        $_SESSION = [];
        session_destroy();
    }
}
