<?php

session_start();

require_once __DIR__ . '/Config/configuration.php';


if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

spl_autoload_register(function (string $class): void {
    $prefixes = [
        'Controller\\' => __DIR__ . '/Controller/',
        'Model\\' => __DIR__ . '/Model/',
    ];

    foreach ($prefixes as $prefix => $baseDir) {
        if (str_starts_with($class, $prefix)) {
            $relative = substr($class, strlen($prefix));
            $file = $baseDir . str_replace('\\', '/', $relative) . '.php';

            if (is_file($file)) {
                require_once $file;
            }

            return;
        }
    }
});

use Controller\ShowController;
use Controller\UserController;

function jsonResponse(array $data, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

function getRequestBody(): array
{
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
    $raw = file_get_contents('php://input');

    if (str_contains($contentType, 'application/json')) {
        $decoded = json_decode($raw, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
            jsonResponse(['error' => 'JSON inválido.'], 422);
        }

        return $decoded;
    }

    parse_str($raw, $parsed);
    return is_array($parsed) ? $parsed : [];
}

$isApiRequest = !empty($_SERVER['PATH_INFO']) || isset($_GET['resource']);

if ($isApiRequest) {
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(204);
        exit;
    }

    $method = $_SERVER['REQUEST_METHOD'];

    if (!empty($_SERVER['PATH_INFO'])) {
        $route = trim($_SERVER['PATH_INFO'], '/');
    } else {
        $route = trim(
            (string) ($_GET['resource'] ?? '') .
            (isset($_GET['id']) ? '/' . (string) $_GET['id'] : ''),
            '/'
        );
    }

    $segments = $route === '' ? [] : explode('/', $route);
    $resource = $segments[0] ?? null;
    $id = $segments[1] ?? null;

    try {
        $usuarioController = new UserController();

        if ($resource === 'auth') {
            $action = $segments[1] ?? null;

            if ($action === 'register') {
                if ($method !== 'POST') {
                    jsonResponse(['error' => 'Método não permitido.'], 405);
                }

                $body = getRequestBody();
                $error = $usuarioController->register(
                    trim((string) ($body['nome'] ?? '')),
                    trim((string) ($body['email'] ?? '')),
                    (string) ($body['senha'] ?? '')
                );

                if ($error) {
                    $status = $error === 'Não foi possível realizar o cadastro.' ? 500 : 422;
                    jsonResponse(['error' => $error], $status);
                }

                jsonResponse(['message' => 'Usuário cadastrado com sucesso.'], 201);
            }

            if ($action === 'login') {
                if ($method !== 'POST') {
                    jsonResponse(['error' => 'Método não permitido.'], 405);
                }

                $body = getRequestBody();
                $email = trim((string) ($body['email'] ?? ''));
                $senha = (string) ($body['senha'] ?? '');

                if (!$usuarioController->login($email, $senha)) {
                    jsonResponse(['error' => 'E-mail ou senha inválidos.'], 401);
                }

                jsonResponse([
                    'message' => 'Login realizado com sucesso.',
                    'user' => $usuarioController->currentUser(),
                ]);
            }

            if ($action === 'logout') {
                if ($method !== 'POST') {
                    jsonResponse(['error' => 'Método não permitido.'], 405);
                }

                if (!$usuarioController->isLoggedIn()) {
                    jsonResponse(['error' => 'Não autenticado.'], 401);
                }

                $usuarioController->logout();
                jsonResponse(['message' => 'Logout realizado com sucesso.']);
            }

            if ($action === 'me') {
                if ($method !== 'GET') {
                    jsonResponse(['error' => 'Método não permitido.'], 405);
                }

                $usuario = $usuarioController->currentUser();

                if (!$usuario) {
                    jsonResponse(['error' => 'Não autenticado.'], 401);
                }

                jsonResponse(['user' => $usuario]);
            }

            jsonResponse(['error' => 'Rota de autenticação não encontrada.'], 404);
        }

        if ($resource === 'shows') {
            if (!$usuarioController->isLoggedIn()) {
                jsonResponse(['error' => 'É necessário estar autenticado.'], 401);
            }

            $showController = new ShowController();
            $idUsuario = (int) $_SESSION['user_id'];

            if ($id === null) {
                if ($method === 'GET') {
                    $shows = $showController->listAll($idUsuario);

                    if ($shows === false) {
                        jsonResponse(['error' => 'Erro ao listar shows.'], 500);
                    }

                    jsonResponse(['data' => $shows]);
                }

                if ($method === 'POST') {
                    $body = getRequestBody();

                    $nome = trim((string) ($body['nome'] ?? ''));
                    $artista = trim((string) ($body['artista'] ?? ''));
                    $local = trim((string) ($body['local'] ?? ''));
                    $dataShow = trim((string) ($body['data_show'] ?? ''));
                    $capacidade = (int) ($body['capacidade'] ?? 0);
                    $precoIngresso = (float) ($body['preco_ingresso'] ?? 0);

                    $error = $showController->validate($nome, $local, $dataShow, $capacidade, $precoIngresso);

                    if ($error) {
                        jsonResponse(['error' => $error], 422);
                    }

                    $show = $showController->create(
                        $nome,
                        $artista,
                        $local,
                        $dataShow,
                        $capacidade,
                        $precoIngresso,
                        $idUsuario
                    );

                    if ($show === false) {
                        jsonResponse(['error' => 'Não foi possível criar o show.'], 500);
                    }

                    jsonResponse([
                        'message' => 'Show criado com sucesso.',
                        'data' => $show,
                    ], 201);
                }

                jsonResponse(['error' => 'Método não permitido para index.php/shows.'], 405);
            }

            if (!ctype_digit((string) $id) || (int) $id <= 0) {
                jsonResponse(['error' => 'ID do show inválido.'], 422);
            }

            $idShow = (int) $id;

            if ($method === 'GET') {
                $show = $showController->find($idShow, $idUsuario);

                if ($show === false) {
                    jsonResponse(['error' => 'Show não encontrado.'], 404);
                }

                jsonResponse(['data' => $show]);
            }

            if ($method === 'PUT') {
                $body = getRequestBody();

                $nome = trim((string) ($body['nome'] ?? ''));
                $artista = trim((string) ($body['artista'] ?? ''));
                $local = trim((string) ($body['local'] ?? ''));
                $dataShow = trim((string) ($body['data_show'] ?? ''));
                $capacidade = (int) ($body['capacidade'] ?? 0);
                $precoIngresso = (float) ($body['preco_ingresso'] ?? 0);

                $error = $showController->validate($nome, $local, $dataShow, $capacidade, $precoIngresso);

                if ($error) {
                    jsonResponse(['error' => $error], 422);
                }

                $updated = $showController->update(
                    $idShow,
                    $nome,
                    $artista,
                    $local,
                    $dataShow,
                    $capacidade,
                    $precoIngresso,
                    $idUsuario
                );

                if ($updated === false) {
                    jsonResponse(['error' => 'Show não encontrado.'], 404);
                }

                jsonResponse([
                    'message' => 'Show atualizado com sucesso.',
                    'data' => $updated,
                ]);
            }

            if ($method === 'DELETE') {
                if (!$showController->delete($idShow, $idUsuario)) {
                    jsonResponse(['error' => 'Show não encontrado.'], 404);
                }

                jsonResponse(['message' => 'Show removido com sucesso.']);
            }

            jsonResponse(['error' => 'Método não permitido para index.php/shows/{id}.'], 405);
        }

        jsonResponse(['error' => 'Rota não encontrada.'], 404);
    } catch (Throwable $e) {
        error_log($e->getMessage());
        jsonResponse(['error' => 'Erro interno no servidor.'], 500);
    }
}

// Interface web de login.
$usuarioController = new UserController();
$errorMessage = '';

if ($usuarioController->isLoggedIn()) {
    header('Location: View/home.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim((string) ($_POST['email'] ?? ''));
    $senha = (string) ($_POST['senha'] ?? '');

    if ($usuarioController->login($email, $senha)) {
        header('Location: View/home.php');
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
    <link rel="stylesheet" href="templates/css/style.css">
</head>

<body>
    <main class="auth-container d-flex justify-content-center align-items-center">
        <div class="card shadow-sm p-4 auth-card">
            <h3 class="text-center mb-1">Gerenciador de Shows</h3>
            <p class="text-center text-muted">Entre com sua conta</p>

            <?php if ($errorMessage): ?>
                <div class="alert alert-danger py-2"><?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label for="email" class="form-label">E-mail</label>
                    <input id="email" type="email" name="email" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="senha" class="form-label">Senha</label>
                    <input id="senha" type="password" name="senha" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary w-100">Entrar</button>
            </form>

            <p class="text-center mt-3 mb-0">
                Não tem conta? <a href="View/register.php">Cadastre-se aqui</a>
            </p>
        </div>
    </main>
</body>

</html>
