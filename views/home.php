<?php
session_start();
require_once __DIR__ . '/../Controller/UserController.php';
require_once __DIR__ . '/../Controller/ShowController.php';

$userController = new UserController();
$showController = new ShowController();

// Protege a página: só usuário logado pode ver o painel
if (!$userController->isLoggedIn()) {
    header('Location: ../index.php');
    exit();
}

$errorMessage = '';
$successMessage = '';

// Cadastro de um novo show
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_show'])) {
    $name = trim($_POST['name']);
    $artist = trim($_POST['artist']);
    $venue = trim($_POST['venue']);
    $date = $_POST['show_date'];
    $capacity = (int) $_POST['capacity'];
    $price = (float) $_POST['ticket_price'];

    $errorMessage = $showController->validate($name, $venue, $date, $capacity, $price);

    if (!$errorMessage) {
        $showController->create($name, $artist, $venue, $date, $capacity, $price, $_SESSION['user_id']);
        $successMessage = 'Show cadastrado com sucesso!';
    }
}

// Exclusão de um show (via link ?delete=ID)
if (isset($_GET['delete'])) {
    $showController->delete((int) $_GET['delete']);
    header('Location: home.php');
    exit();
}

$shows = $showController->listAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciador de Shows | Painel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <nav class="navbar navbar-dark bg-dark px-3 mb-4">
        <span class="navbar-brand">🎤 Gerenciador de Shows</span>
        <span class="text-white">
            Olá, <?= htmlspecialchars($_SESSION['user_name']) ?> —
            <a href="logout.php" class="text-white">Sair</a>
        </span>
    </nav>

    <div class="container">

        <?php if ($errorMessage): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($errorMessage) ?></div>
        <?php endif; ?>
        <?php if ($successMessage): ?>
            <div class="alert alert-success"><?= htmlspecialchars($successMessage) ?></div>
        <?php endif; ?>

        <div class="row">
            <!-- Formulário de cadastro -->
            <div class="col-md-4">
                <div class="card shadow-sm p-3 mb-4">
                    <h5>Cadastrar novo show</h5>
                    <form method="POST">
                        <input type="hidden" name="create_show" value="1">

                        <div class="mb-2">
                            <label class="form-label">Nome do show</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Artista</label>
                            <input type="text" name="artist" class="form-control">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Local</label>
                            <input type="text" name="venue" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Data e hora</label>
                            <input type="datetime-local" name="show_date" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Capacidade</label>
                            <input type="number" name="capacity" class="form-control" min="1" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Valor do ingresso (R$)</label>
                            <input type="number" step="0.01" name="ticket_price" class="form-control" min="0" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Cadastrar</button>
                    </form>
                </div>
            </div>

            <!-- Lista de shows cadastrados -->
            <div class="col-md-8">
                <h5>Shows cadastrados</h5>

                <?php if (empty($shows)): ?>
                    <p class="text-muted">Nenhum show cadastrado ainda.</p>
                <?php endif; ?>

                <?php foreach ($shows as $show): ?>
                    <div class="card shadow-sm p-3 mb-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1"><?= htmlspecialchars($show['name']) ?></h6>
                                <p class="mb-1 text-muted">
                                    <?= htmlspecialchars($show['artist'] ?: 'Artista não informado') ?>
                                    — <?= htmlspecialchars($show['venue']) ?>
                                </p>
                                <p class="mb-0 small">
                                    📅 <?= date('d/m/Y H:i', strtotime($show['show_date'])) ?>
                                    &nbsp;|&nbsp; 🎟 <?= (int) $show['capacity'] ?> vagas
                                    &nbsp;|&nbsp; 💰 R$ <?= number_format((float) $show['ticket_price'], 2, ',', '.') ?>
                                </p>
                            </div>
                            <a href="home.php?delete=<?= (int) $show['id'] ?>" class="btn btn-sm btn-outline-danger"
                                onclick="return confirm('Excluir este show?')">Excluir</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</body>

</html>
