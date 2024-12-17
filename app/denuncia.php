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

$apiUrl = "http://localhost/NotificaPHP/api/denuncias";

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
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-blue-50">
<div class="container mx-auto mt-5 p-4">
    <h1 class="text-5xl font-semibold text-blue-700 mb-6">Gerenciar Denúncias</h1>

    <!-- Formulário de Criação/Edição -->
    <form method="POST" enctype="multipart/form-data" class="space-y-4 bg-white p-6 rounded-lg shadow-lg">
        <input type="hidden" name="action" value="<?= $editData ? 'edit' : 'create' ?>">
        <?php if ($editData): ?>
            <input type="hidden" name="id_denuncias" value="<?= $editData['id_denuncias'] ?>">
        <?php endif; ?>
        <div class="mb-4">
            <label for="titulo" class="form-label">Título</label>
            <input type="text" class="form-control" id="titulo" name="titulo" value="<?= $editData['titulo'] ?? '' ?>" required>
        </div>
        <div class="mb-4">
            <label for="descricao" class="form-label">Descrição</label>
            <textarea class="form-control" id="descricao" name="descricao" rows="3" required><?= $editData['descricao'] ?? '' ?></textarea>
        </div>
        <div class="mb-4">
            <label for="categoria" class="form-label">Categoria</label>
            <select class="form-select p-3 w-full border border-gray-300 rounded-md" id="categoria" name="categoria" required>
                <option value="agua" <?= isset($editData['categoria']) && $editData['categoria'] === 'agua' ? 'selected' : '' ?>>Água</option>
                <option value="saneamento" <?= isset($editData['categoria']) && $editData['categoria'] === 'saneamento' ? 'selected' : '' ?>>Saneamento</option>
                <option value="obras" <?= isset($editData['categoria']) && $editData['categoria'] === 'obras' ? 'selected' : '' ?>>Obras</option>
                <option value="outros" <?= isset($editData['categoria']) && $editData['categoria'] === 'outros' ? 'selected' : '' ?>>Outros</option>
            </select>
        </div>
        <div class="mb-4">
            <label for="status" class="form-label">Status</label>
            <select class="form-select p-3 w-full border border-gray-300 rounded-md" id="status" name="status" required>
                <option value="Pendente" <?= isset($editData['status']) && $editData['status'] === 'Pendente' ? 'selected' : '' ?>>Pendente</option>
                <option value="Em andamento" <?= isset($editData['status']) && $editData['status'] === 'Em andamento' ? 'selected' : '' ?>>Em andamento</option>
                <option value="Resolvido" <?= isset($editData['status']) && $editData['status'] === 'Resolvido' ? 'selected' : '' ?>>Resolvido</option>
            </select>
        </div>
        <div class="mb-4">
            <label for="Usuarios_id_usuario" class="form-label">Usuário</label>
            <input type="number" class="form-control p-3 w-full border border-gray-300 rounded-md" id="Usuarios_id_usuario" name="Usuarios_id_usuario" value="<?= $editData['Usuarios_id_usuario'] ?? '' ?>" required>
        </div>
        <div class="mb-4">
            <label for="imagem" class="form-label">Imagem</label>
            <input type="file" class="form-control p-3 w-full border border-gray-300 rounded-md" id="imagem" name="imagem">
        </div>
        <div class="mb-4">
            <label for="localizacao" class="form-label">Localização</label>
            <input type="text" class="form-control p-3 w-full border border-gray-300 rounded-md" id="localizacao" name="localizacao" value="<?= $editData['localizacao'] ?? '' ?>">
        </div>
        <div class="form-check mb-4">
            <input class="form-check-input" type="checkbox" id="anonimo" name="anonimo" <?= isset($editData['anonimo']) && $editData['anonimo'] ? 'checked' : '' ?>>
            <label class="form-check-label" for="anonimo">Anônimo</label>
        </div>
        <button type="submit" class="btn text-white bg-blue-600 px-6 py-3 rounded-md">Salvar</button>
    </form>

        <!-- Formulário de Filtro -->
    <div class="mt-6 overflow-x-auto bg-white rounded-lg shadow-lg p-4">
        <form method="GET" action="denuncia.php" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status:</label>
                    <select name="status" id="status" class="form-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="" <?= empty($_GET['status']) ? 'selected' : '' ?>>Todos</option>
                        <option value="Pendente" <?= $_GET['status'] === 'pendente' ? 'selected' : '' ?>>Pendente</option>
                        <option value="Em Andamento" <?= $_GET['status'] === 'em andamento' ? 'selected' : '' ?>>Em Andamento</option>
                        <option value="Resolvido" <?= $_GET['status'] === 'resolvido' ? 'selected' : '' ?>>Resolvido</option>
                    </select>
                </div>

                <div>
                    <label for="usuario" class="block text-sm font-medium text-gray-700">Usuário:</label>
                    <input type="text" name="usuario" id="usuario" placeholder="Digite o nome do usuário" value="<?= htmlspecialchars($_GET['usuario'] ?? '') ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>

                <div>
                    <label for="data_inicio" class="block text-sm font-medium text-gray-700">Data Início:</label>
                    <input type="date" name="data_inicio" id="data_inicio" value="<?= htmlspecialchars($_GET['data_inicio'] ?? '') ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>

                <div>
                    <label for="data_fim" class="block text-sm font-medium text-gray-700">Data Fim:</label>
                    <input type="date" name="data_fim" id="data_fim" value="<?= htmlspecialchars($_GET['data_fim'] ?? '') ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
            </div>

            <button type="submit" class="mt-3 w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Filtrar
            </button>
        </form>
    </div>

    <!-- Tabela de Listagem -->
    <div class="mt-6 overflow-x-auto bg-white rounded-lg shadow-lg">
        <table class="min-w-full table-auto">
            <thead class="bg-blue-600 text-white">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-medium">ID</th>
                    <th class="px-6 py-3 text-left text-sm font-medium">Título</th>
                    <th class="px-6 py-3 text-left text-sm font-medium">Descrição</th>
                    <th class="px-6 py-3 text-left text-sm font-medium">Categoria</th>
                    <th class="px-6 py-3 text-left text-sm font-medium">Status</th>
                    <th class="px-6 py-3 text-left text-sm font-medium">Localização</th>
                    <th class="px-6 py-3 text-left text-sm font-medium">Imagem</th>
                    <th class="px-6 py-3 text-left text-sm font-medium">Ações</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                <?php if (!empty($denuncias)): ?>
                    <?php foreach ($denuncias as $denuncia): ?>
                        <tr class="border-t hover:bg-blue-50">
                            <td class="px-6 py-3"><?= htmlspecialchars($denuncia['id_denuncias']) ?></td>
                            <td class="px-6 py-3"><?= htmlspecialchars($denuncia['titulo']) ?></td>
                            <td class="px-6 py-3"><?= htmlspecialchars($denuncia['descricao']) ?></td>
                            <td class="px-6 py-3"><?= htmlspecialchars($denuncia['categoria']) ?></td>
                            <td class="px-6 py-3"><?= htmlspecialchars($denuncia['status']) ?></td>
                            <td class="px-6 py-3"><?= htmlspecialchars($denuncia['localizacao'] ?? 'Não informado') ?></td>
                            <td class="px-6 py-3">
                                <?php if (!empty($denuncia['imagem'])): ?>
                                    <img src="<?= htmlspecialchars($denuncia['imagem']) ?>" alt="Imagem da denúncia" style="max-width: 100px; max-height: 100px;">
                                <?php else: ?>
                                    Não disponível
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-3 flex space-x-2">
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="id_denuncias" value="<?= htmlspecialchars($denuncia['id_denuncias']) ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <button type="submit" class="btn text-white bg-red-600 hover:bg-red-800 btn-sm">Excluir</button>
                                </form>
                                <a href="?edit_id=<?= htmlspecialchars($denuncia['id_denuncias']) ?>" class="btn btn-warning hover:bg-yellow-600 btn-sm">Editar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center py-3 text-gray-500">Nenhuma denúncia encontrada.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    </div>
    <a href="index.html" class="btn btn-primary m-3">Voltar</a>
</div>
</body>
</html>
