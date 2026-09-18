<?php
session_start();
require_once __DIR__ . '/Controller/UserController.php';

$userController = new UserController();
$errorMessage = '';

// Se já estiver logado, vai direto pro painel
if ($userController->isLoggedIn()) {
    header('Location: views/home.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    if ($userController->login($email, $password)) {
        header('Location: views/home.php');
        exit();
    }

    $errorMessage = 'E-mail ou senha inválidos!';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciador de Shows | Entrar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <main class="d-flex justify-content-center align-items-center vh-100">
        <div class="card shadow-sm p-4" style="width: 100%; max-width: 380px;">
            <h3 class="text-center mb-1">🎤 Gerenciador de Shows</h3>
            <p class="text-center text-muted">Entre com sua conta</p>

            <?php if ($errorMessage): ?>
                <div class="alert alert-danger py-2"><?= htmlspecialchars($errorMessage) ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">E-mail</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Senha</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Entrar</button>
            </form>

            <p class="text-center mt-3 mb-0">
                Não tem conta? <a href="views/register.php">Cadastre-se aqui</a>
            </p>
        </div>
    </main>
</body>

</html>
