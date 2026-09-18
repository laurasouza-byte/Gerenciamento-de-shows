<?php

session_start();

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../Model/Connection.php';
require_once __DIR__ . '/../Model/User.php';
require_once __DIR__ . '/../Controller/UserController.php';

use Controller\UserController;

$userController = new UserController();

if (!$userController->isLoggedIn()) {
    header('Location: ../index.php');
    exit();
}

$userName = $_SESSION['user_name'] ?? '';
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciador de Shows | Painel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../templates/css/style.css">
</head>

<body>
    <nav class="navbar navbar-dark bg-dark px-3 mb-4">
        <span class="navbar-brand">Gerenciador de Shows</span>

        <span class="text-white">
            Olá, <?= htmlspecialchars($userName, ENT_QUOTES, 'UTF-8') ?> —
            <a href="logout.php" class="text-white">Sair</a>
        </span>
    </nav>

    <div class="container">
        <div id="alertBox" aria-live="polite"></div>

        <div class="row">
            <div class="col-md-4">
                <div class="card shadow-sm p-3 mb-4">
                    <h5 id="formTitle">Cadastrar novo show</h5>

                    <form id="showForm">
                        <input type="hidden" name="id" id="showId">

                        <div class="mb-2">
                            <label for="nome" class="form-label">Nome do show</label>
                            <input id="nome" type="text" name="nome" class="form-control" required>
                        </div>

                        <div class="mb-2">
                            <label for="artista" class="form-label">Artista</label>
                            <input id="artista" type="text" name="artista" class="form-control">
                        </div>

                        <div class="mb-2">
                            <label for="local" class="form-label">Local</label>
                            <input id="local" type="text" name="local" class="form-control" required>
                        </div>

                        <div class="mb-2">
                            <label for="showDate" class="form-label">Data e hora</label>
                            <input id="showDate" type="datetime-local" name="data_show" class="form-control" required>
                        </div>

                        <div class="mb-2">
                            <label for="capacidade" class="form-label">Capacidade</label>
                            <input id="capacidade" type="number" name="capacidade" class="form-control" min="1" required>
                        </div>

                        <div class="mb-3">
                            <label for="ticketPrice" class="form-label">Valor do ingresso (R$)</label>
                            <input id="ticketPrice" type="number" step="0.01" name="preco_ingresso" class="form-control" min="0" required>
                        </div>

                        <button type="submit" id="submitButton" class="btn btn-primary w-100">
                            Cadastrar
                        </button>

                        <button type="button" id="cancelButton" class="btn btn-secondary w-100 mt-2 d-none">
                            Cancelar edição
                        </button>
                    </form>
                </div>
            </div>

            <div class="col-md-8">
                <h5>Meus shows</h5>

                <div id="showsList">
                    <p class="text-muted">Carregando...</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        const API_URL = '../index.php/shows';

        const showsList = document.getElementById('showsList');
        const alertBox = document.getElementById('alertBox');
        const showForm = document.getElementById('showForm');
        const showId = document.getElementById('showId');
        const formTitle = document.getElementById('formTitle');
        const submitButton = document.getElementById('submitButton');
        const cancelButton = document.getElementById('cancelButton');

        function showAlert(message, type = 'success') {
            alertBox.replaceChildren();

            const alert = document.createElement('div');
            alert.className = `alert alert-${type}`;
            alert.textContent = message;

            alertBox.appendChild(alert);

            setTimeout(() => alert.remove(), 4000);
        }

        function formatCurrency(value) {
            return Number(value).toLocaleString('pt-BR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        function formatDate(value) {
            const date = new Date(value);

            if (Number.isNaN(date.getTime())) {
                return 'Data inválida';
            }

            return date.toLocaleString('pt-BR', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        function createTextElement(tag, text, className = '') {
            const element = document.createElement(tag);
            element.textContent = text;

            if (className) {
                element.className = className;
            }

            return element;
        }

        function renderShows(shows) {
            showsList.replaceChildren();

            if (shows.length === 0) {
                showsList.appendChild(
                    createTextElement(
                        'p',
                        'Nenhum show cadastrado ainda.',
                        'text-muted'
                    )
                );

                return;
            }

            shows.forEach((show) => {
                const card = document.createElement('div');
                card.className = 'card shadow-sm p-3 mb-3';

                const row = document.createElement('div');
                row.className = 'd-flex justify-content-between align-items-start gap-3';

                const content = document.createElement('div');
                content.className = 'flex-grow-1';

                content.appendChild(
                    createTextElement('h6', show.nome, 'mb-1')
                );

                const artistText = show.artista
                    ? `${show.artista} — ${show.local}`
                    : `Artista não informado — ${show.local}`;

                content.appendChild(
                    createTextElement('p', artistText, 'mb-1 text-muted')
                );

                const details = createTextElement(
                    'p',
                    `📅 ${formatDate(show.data_show)} | 🎟 ${show.capacidade} vagas | 💰 R$ ${formatCurrency(show.preco_ingresso)}`,
                    'mb-0 small'
                );

                content.appendChild(details);

                const actions = document.createElement('div');
                actions.className = 'd-flex gap-2 flex-shrink-0';

                const editButton = createTextElement(
                    'button',
                    'Editar',
                    'btn btn-sm btn-outline-primary'
                );

                editButton.type = 'button';
                editButton.dataset.editId = show.id;

                const deleteButton = createTextElement(
                    'button',
                    'Excluir',
                    'btn btn-sm btn-outline-danger'
                );

                deleteButton.type = 'button';
                deleteButton.dataset.deleteId = show.id;

                actions.append(editButton, deleteButton);
                row.append(content, actions);
                card.appendChild(row);
                showsList.appendChild(card);
            });
        }

        async function loadShows() {
            try {
                const response = await fetch(API_URL, {
                    method: 'GET'
                });

                const payload = await response.json();

                if (!response.ok) {
                    showAlert(
                        payload.error ?? 'Erro ao carregar shows.',
                        'danger'
                    );

                    return;
                }

                renderShows(payload.data ?? []);

            } catch (error) {
                showAlert(
                    'Não foi possível conectar à API.',
                    'danger'
                );
            }
        }

        function startEdit(show) {
            showId.value = show.id;

            document.getElementById('nome').value = show.nome ?? '';
            document.getElementById('artista').value = show.artista ?? '';
            document.getElementById('local').value = show.local ?? '';

            document.getElementById('showDate').value =
                String(show.data_show ?? '')
                    .replace(' ', 'T')
                    .slice(0, 16);

            document.getElementById('capacidade').value =
                show.capacidade ?? '';

            document.getElementById('ticketPrice').value =
                show.preco_ingresso ?? '';

            formTitle.textContent = 'Editar show';
            submitButton.textContent = 'Salvar alterações';

            cancelButton.classList.remove('d-none');

            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        function resetForm() {
            showForm.reset();
            showId.value = '';

            formTitle.textContent = 'Cadastrar novo show';
            submitButton.textContent = 'Cadastrar';

            cancelButton.classList.add('d-none');
        }

        showForm.addEventListener('submit', async (event) => {
            event.preventDefault();

            const formData = new FormData(showForm);
            const body = Object.fromEntries(formData.entries());

            const id = showId.value;

            delete body.id;

            const method = id ? 'PUT' : 'POST';

            const url = id
                ? `${API_URL}/${encodeURIComponent(id)}`
                : API_URL;

            try {
                const response = await fetch(url, {
                    method,
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(body)
                });

                const payload = await response.json();

                if (!response.ok) {
                    showAlert(
                        payload.error ?? 'Erro ao salvar show.',
                        'danger'
                    );

                    return;
                }

                showAlert(
                    payload.message ?? 'Show salvo com sucesso!'
                );

                resetForm();

                await loadShows();

            } catch (error) {
                showAlert(
                    'Não foi possível conectar à API.',
                    'danger'
                );
            }
        });

        showsList.addEventListener('click', async (event) => {
            const editButton =
                event.target.closest('[data-edit-id]');

            const deleteButton =
                event.target.closest('[data-delete-id]');

            if (editButton) {
                try {
                    const id = editButton.dataset.editId;

                    const response = await fetch(
                        `${API_URL}/${encodeURIComponent(id)}`
                    );

                    const payload = await response.json();

                    if (!response.ok) {
                        showAlert(
                            payload.error ?? 'Erro ao buscar show.',
                            'danger'
                        );

                        return;
                    }

                    startEdit(payload.data);

                } catch (error) {
                    showAlert(
                        'Não foi possível conectar à API.',
                        'danger'
                    );
                }

                return;
            }

            if (!deleteButton) {
                return;
            }

            if (!confirm('Excluir este show?')) {
                return;
            }

            try {
                const id = deleteButton.dataset.deleteId;

                const response = await fetch(
                    `${API_URL}/${encodeURIComponent(id)}`,
                    {
                        method: 'DELETE'
                    }
                );

                const payload = await response.json();

                if (!response.ok) {
                    showAlert(
                        payload.error ?? 'Erro ao excluir show.',
                        'danger'
                    );

                    return;
                }

                showAlert(
                    payload.message ?? 'Show removido com sucesso!'
                );

                await loadShows();

            } catch (error) {
                showAlert(
                    'Não foi possível conectar à API.',
                    'danger'
                );
            }
        });

        cancelButton.addEventListener('click', resetForm);

        loadShows();
    </script>
</body>

</html>