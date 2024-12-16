<?php
function callApi(string $method, string $url, array $data = []): array {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

    if (!empty($data)) {
        $postData = [];
        if (isset($data['imagem']) && is_array($data['imagem']) && !empty($data['imagem']['tmp_name'])) {
            $fileData = $data['imagem'];
            unset($data['imagem']);
            $postData = [
                'imagem' => new CURLFile($fileData['tmp_name'], $fileData['type'], $fileData['name']),
            ];
        }
        $postData = array_merge($postData, [
            'titulo' => $data['titulo'] ?? '',
            'descricao' => $data['descricao'] ?? '',
            'categoria' => $data['categoria'] ?? '',
            'status' => $data['status'] ?? '',
            'Usuarios_id_usuario' => $data['Usuarios_id_usuario'] ?? '',
            'anonimo' => $data['anonimo'] ?? false,
            'localizacao' => $data['localizacao'] ?? '',
        ]);
        if (!empty($data)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        }
    }

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return ['data' => json_decode($response, true), 'http_code' => $httpCode];
}

$apiUrl = "http://localhost/api/denuncias";

// Tratamento de POST para criação e edição
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'create') {
        $data = [
            'titulo' => $_POST['titulo'] ?? '',
            'descricao' => $_POST['descricao'] ?? '',
            'categoria' => $_POST['categoria'] ?? '',
            'status' => $_POST['status'] ?? '',
            'Usuarios_id_usuario' => $_POST['Usuarios_id_usuario'] ?? '',
            'anonimo' => isset($_POST['anonimo']) ? true : false,
            'localizacao' => $_POST['localizacao'] ?? '',
        ];

        if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === 0) {
            $fileTmpName = $_FILES['imagem']['tmp_name'];
            $fileContent = file_get_contents($fileTmpName);
            $base64Image = base64_encode($fileContent);
            $data['imagem'] = "data:image/jpeg;base64," . $base64Image;
        }

        $response = callApi('POST', $apiUrl, $data);
        echo '<pre>';
        echo "Dados enviados para a API:\n";
        print_r($data);
        echo "\nResposta da API:\n";
        print_r($response);
        echo '</pre>';

    } elseif (isset($_POST['action']) && $_POST['action'] === 'edit' && isset($_POST['id_denuncias'])) {
        $data = [
            'titulo' => $_POST['titulo'] ?? '',
            'descricao' => $_POST['descricao'] ?? '',
            'categoria' => $_POST['categoria'] ?? '',
            'status' => $_POST['status'] ?? '',
            'Usuarios_id_usuario' => $_POST['Usuarios_id_usuario'] ?? '',
            'anonimo' => isset($_POST['anonimo']) ? true : false,
            'localizacao' => $_POST['localizacao'] ?? '',
        ];

        if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === 0) {
            $fileTmpName = $_FILES['imagem']['tmp_name'];
            $fileContent = file_get_contents($fileTmpName);
            $base64Image = base64_encode($fileContent);
            $data['imagem'] = "data:image/jpeg;base64," . $base64Image; 
        }

        $response = callApi('PUT', "$apiUrl/{$_POST['id_denuncias']}", $data);
        echo '<pre>';
        echo "Dados enviados para a API:\n";
        print_r($data);
        echo "\nResposta da API:\n";
        print_r($response);
        echo '</pre>';
    }
}

// DELETE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    if (isset($_POST['id_denuncias'])) {
        $response = callApi('DELETE', "$apiUrl/{$_POST['id_denuncias']}");
    }
}

// Listar denúncias
$response = callApi('GET', $apiUrl);
$denuncias = $response['http_code'] === 200 ? $response['data'] : [];

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
    <title>Gerenciar Denúncias</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h1>Gerenciar Denúncias</h1>

    <!-- Formulário de Criação/Edição -->
    <form method="POST" enctype="multipart/form-data" class="mb-4">
        <input type="hidden" name="action" value="<?= $editData ? 'edit' : 'create' ?>">
        <?php if ($editData): ?>
            <input type="hidden" name="id_denuncias" value="<?= $editData['id_denuncias'] ?>">
        <?php endif; ?>
        <div class="mb-3">
            <label for="titulo" class="form-label">Título</label>
            <input type="text" class="form-control" id="titulo" name="titulo" value="<?= $editData['titulo'] ?? '' ?>" required>
        </div>
        <div class="mb-3">
            <label for="descricao" class="form-label">Descrição</label>
            <textarea class="form-control" id="descricao" name="descricao" rows="3" required><?= $editData['descricao'] ?? '' ?></textarea>
        </div>
        <div class="mb-3">
            <label for="categoria" class="form-label">Categoria</label>
            <select class="form-select" id="categoria" name="categoria" required>
                <option value="agua" <?= isset($editData['categoria']) && $editData['categoria'] === 'agua' ? 'selected' : '' ?>>Água</option>
                <option value="saneamento" <?= isset($editData['categoria']) && $editData['categoria'] === 'saneamento' ? 'selected' : '' ?>>Saneamento</option>
                <option value="obras" <?= isset($editData['categoria']) && $editData['categoria'] === 'obras' ? 'selected' : '' ?>>Obras</option>
                <option value="outros" <?= isset($editData['categoria']) && $editData['categoria'] === 'outros' ? 'selected' : '' ?>>Outros</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select class="form-select" id="status" name="status" required>
                <option value="Pendente" <?= isset($editData['status']) && $editData['status'] === 'Pendente' ? 'selected' : '' ?>>Pendente</option>
                <option value="Em andamento" <?= isset($editData['status']) && $editData['status'] === 'Em andamento' ? 'selected' : '' ?>>Em andamento</option>
                <option value="Resolvido" <?= isset($editData['status']) && $editData['status'] === 'Resolvido' ? 'selected' : '' ?>>Resolvido</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="Usuarios_id_usuario" class="form-label">Usuário</label>
            <input type="number" class="form-control" id="Usuarios_id_usuario" name="Usuarios_id_usuario" value="<?= $editData['Usuarios_id_usuario'] ?? '' ?>" required>
        </div>
        <div class="mb-3">
            <label for="imagem" class="form-label">Imagem</label>
            <input type="file" class="form-control" id="imagem" name="imagem">
        </div>
        <div class="mb-3">
            <label for="localizacao" class="form-label">Localização</label>
            <input type="text" class="form-control" id="localizacao" name="localizacao" value="<?= $editData['localizacao'] ?? '' ?>">
        </div>
        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" id="anonimo" name="anonimo" <?= isset($editData['anonimo']) && $editData['anonimo'] ? 'checked' : '' ?>>
            <label class="form-check-label" for="anonimo">Anônimo</label>
        </div>
        <button type="submit" class="btn btn-primary">Salvar</button>
    </form>

    <!-- Tabela de Listagem -->
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Descrição</th>
                <th>Categoria</th>
                <th>Status</th>
                <th>Localização</th>
                <th>Imagem</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($denuncias)): ?>
                <?php foreach ($denuncias as $denuncia): ?>
                    <tr>
                        <td><?= htmlspecialchars($denuncia['id_denuncias']) ?></td>
                        <td><?= htmlspecialchars($denuncia['titulo']) ?></td>
                        <td><?= htmlspecialchars($denuncia['descricao']) ?></td>
                        <td><?= htmlspecialchars($denuncia['categoria']) ?></td>
                        <td><?= htmlspecialchars($denuncia['status']) ?></td>
                        <td><?= htmlspecialchars($denuncia['localizacao'] ?? 'Não informado') ?></td>
                        <td>
                            <?php if (!empty($denuncia['imagem'])): ?>
                                <img src="<?= htmlspecialchars($denuncia['imagem']) ?>" alt="Imagem da denúncia" style="max-width: 100px; max-height: 100px;">
                            <?php else: ?>
                                Não disponível
                            <?php endif; ?>
                        </td>
                        <td>
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="id_denuncias" value="<?= htmlspecialchars($denuncia['id_denuncias']) ?>">
                                <input type="hidden" name="action" value="delete">
                                <button type="submit" class="btn btn-danger btn-sm">Excluir</button>
                            </form>
                            <a href="?edit_id=<?= htmlspecialchars($denuncia['id_denuncias']) ?>" class="btn btn-warning btn-sm">Editar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" class="text-center">Nenhuma denúncia encontrada.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
