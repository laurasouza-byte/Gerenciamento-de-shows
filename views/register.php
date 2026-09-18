<?php
session_start();
require_once __DIR__ . '/../Controller/UserController.php';

$userController = new UserController();
$errorMessage = '';
$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    $errorMessage = $userController->register($name, $email, $password);

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
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <main class="d-flex justify-content-center align-items-center vh-100">
        <div class="card shadow-sm p-4" style="width: 100%; max-width: 380px;">
            <h3 class="text-center mb-3">Criar Conta</h3>

            <?php if ($errorMessage): ?>
                <div class="alert alert-danger py-2"><?= htmlspecialchars($errorMessage) ?></div>
            <?php endif; ?>
            <?php if ($successMessage): ?>
                <div class="alert alert-success py-2"><?= htmlspecialchars($successMessage) ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Nome</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">E-mail</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Senha</label>
                    <input type="password" name="password" class="form-control" required minlength="6">
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
