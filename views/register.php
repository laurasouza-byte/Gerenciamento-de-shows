<?php

session_start();
require_once __DIR__ . '/../vendor/autoload.php';

use Controller\UserController;

$userController = new UserController();
$errorMessage = '';
$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim((string) ($_POST['nome'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $senha = (string) ($_POST['senha'] ?? '');

    $errorMessage = $userController->register($nome, $email, $senha);

    if (!$errorMessage) {
        $successMessage = 'Cadastro realizado com sucesso! Você já pode entrar.';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciador de Shows | Cadastro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../templates/css/style.css">
</head>

<body>
    <main class="d-flex justify-content-center align-items-center vh-100">
        <div class="card shadow-sm p-4" class="auth-card">
            <h3 class="text-center mb-3">Criar Conta</h3>

            <?php if ($errorMessage): ?>
                <div class="alert alert-danger py-2"><?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?></div>

            <?php endif; ?>

            <?php if ($successMessage): ?>
                <div class="alert alert-success py-2"><?= htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8') ?></div>

            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Nome</label>
                    <input type="text" name="nome" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">E-mail</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Senha</label>
                    <input type="password" name="senha" class="form-control" required minlength="6">
                </div>
                <button type="submit" class="btn btn-primary w-100">Cadastrar</button>
            </form>

            <p class="text-center mt-3 mb-0">
                Já tem conta? <a href="../index.php">Entrar</a>
            </p>

        </div>
    </main>
</body>

</html>
