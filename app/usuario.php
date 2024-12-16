<?php
function callApi(string $method, string $url, array $data = []): array {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

    if (!empty($data)) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    }

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return ['data' => json_decode($response, true), 'http_code' => $httpCode];
}

$apiUrl = "http://localhost/NotificaPHP/api/usuarios";

// Tratamento de POST para criação e edição
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'create') {
        $data = [
            'cpf_usuario' => $_POST['cpf_usuario'] ?? '',
            'nome_usuario' => $_POST['nome_usuario'] ?? '',
            'telefone' => $_POST['telefone'] ?? '',
            'senha' => $_POST['senha'] ?? '',
        ];

        $response = callApi('POST', $apiUrl, $data);
        echo '<pre>Resposta da API: ';
        print_r($response);
        echo '</pre>';

    } elseif (isset($_POST['action']) && $_POST['action'] === 'edit' && isset($_POST['Usuario_id_usuario'])) {
        $data = [
            'cpf_usuario' => $_POST['cpf_usuario'] ?? '',
            'nome_usuario' => $_POST['nome_usuario'] ?? '',
            'telefone' => $_POST['telefone'] ?? '',
            'senha' => $_POST['senha'] ?? '',
        ];

        $response = callApi('PUT', "$apiUrl/{$_POST['Usuario_id_usuario']}", $data);
        echo '<pre>Resposta da API: ';
        print_r($response);
        echo '</pre>';
    }
}

// DELETE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    if (isset($_POST['Usuario_id_usuario'])) {
        $response = callApi('DELETE', "$apiUrl/{$_POST['Usuario_id_usuario']}");
    }
}

// Listar usuários
$response = callApi('GET', $apiUrl);
$usuarios = $response['http_code'] === 200 ? $response['data'] : [];

// Preencher o formulário com dados para edição
$editData = null;
if (isset($_GET['edit_id'])) {
    $editResponse = callApi('GET', "$apiUrl/{$_GET['edit_id']}");
    $editData = $editResponse['data'] ?? null;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Usuários</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h1>Gerenciar Usuários</h1>

    <!-- Formulário de Criação/Edição -->
    <form method="POST" class="mb-4">
        <input type="hidden" name="action" value="<?= $editData ? 'edit' : 'create' ?>">
        <?php if ($editData): ?>
            <input type="hidden" name="Usuario_id_usuario" value="<?= $editData['Usuario_id_usuario'] ?>">
        <?php endif; ?>
        <div class="mb-3">
            <label for="cpf_usuario" class="form-label">CPF</label>
            <input type="text" class="form-control" id="cpf_usuario" name="cpf_usuario" value="<?= $editData['cpf_usuario'] ?? '' ?>" required>
        </div>
        <div class="mb-3">
            <label for="nome_usuario" class="form-label">Nome</label>
            <input type="text" class="form-control" id="nome_usuario" name="nome_usuario" value="<?= $editData['nome_usuario'] ?? '' ?>" required>
        </div>
        <div class="mb-3">
            <label for="telefone" class="form-label">Telefone</label>
            <input type="text" class="form-control" id="telefone" name="telefone" value="<?= $editData['telefone'] ?? '' ?>" required>
        </div>
        <div class="mb-3">
            <label for="senha" class="form-label">Senha</label>
            <input type="password" class="form-control" id="senha" name="senha" <?= $editData ? '' : 'required' ?>>
        </div>
        <button type="submit" class="btn btn-primary">Salvar</button>
    </form>

    <!-- Tabela de Listagem -->
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>CPF</th>
                <th>Nome</th>
                <th>Telefone</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($usuarios)): ?>
                <?php foreach ($usuarios as $usuario): ?>
                    <tr>
                        <td><?= htmlspecialchars($usuario['Usuario_id_usuario']) ?></td>
                        <td><?= htmlspecialchars($usuario['cpf_usuario']) ?></td>
                        <td><?= htmlspecialchars($usuario['nome_usuario']) ?></td>
                        <td><?= htmlspecialchars($usuario['telefone']) ?></td>
                        <td>
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="Usuario_id_usuario" value="<?= htmlspecialchars($usuario['Usuario_id_usuario']) ?>">
                                <input type="hidden" name="action" value="delete">
                                <button type="submit" class="btn btn-danger btn-sm">Excluir</button>
                            </form>
                            <a href="?edit_id=<?= htmlspecialchars($usuario['Usuario_id_usuario']) ?>" class="btn btn-warning btn-sm">Editar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center">Nenhum usuário encontrado.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
