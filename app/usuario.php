<?php
$message = '';
// Função para chamar a API
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

// URL da API
$apiUrl = "http://localhost/api/usuarios";


// Processamento de POST para criação e edição
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        // Criação de usuário
        if ($_POST['action'] === 'create') {
            $data = [
                'cpf_usuario' => $_POST['cpf_usuario'] ?? '',
                'nome_usuario' => $_POST['nome_usuario'] ?? '',
                'telefone' => $_POST['telefone'] ?? '',
                'senha' => $_POST['senha'] ?? '',
            ];

            $response = callApi('POST', $apiUrl, $data);
            // echo '<pre>';
            // echo "Dados enviados para a API:\n";
            // print_r($data);
            // echo "\nResposta da API:\n";
            // print_r($response);
            // echo '</pre>';
            if ($response['http_code'] === 201) {
                $message = 'Usuário criado com sucesso!';
            } else {
                $message = 'Erro ao criar usuário. Tente novamente. ' . $response['data']['message'];
            }

        // Edição de usuário
        } elseif (isset($_POST['action']) && $_POST['action'] === 'edit' && isset($_POST['id_usuario'])) {
            $data = [
                'cpf_usuario' => $_POST['cpf_usuario'] ?? '',
                'nome_usuario' => $_POST['nome_usuario'] ?? '',
                'telefone' => $_POST['telefone'] ?? '',
                'senha' => $_POST['senha'] ?? '',
            ];

            $response = callApi('PUT', "$apiUrl/{$_POST['id_usuario']}", $data);
            // echo '<pre>';
            // echo "Dados enviados para a API:\n";
            // print_r($data);
            // echo "\nResposta da API:\n";
            // print_r($response);
            // echo '</pre>';
            if ($response['http_code'] === 200) {
                $message = 'Usuário atualizado com sucesso!';
            } else {
                $message = 'Erro ao atualizar usuário. Tente novamente.';
            }
        }
    }
}


// Deletar usuário
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete' && isset($_POST['id_usuario'])) {
    $response = callApi('DELETE', "$apiUrl/{$_POST['id_usuario']}");

    if($response['http_code'] === 204) {
        $message = 'Usuário excluído com sucesso!';
    } else {
        $message = 'Erro ao excluir usuário. Tente novamente.';
    }

}

// Listar usuários (GET)
$response = callApi('GET', $apiUrl);
$usuarios = $response['http_code'] === 200 ? $response['data'] : [];

// Criando a URL com os parâmetros de filtro
$queryParams = [];
if (!empty($_GET['nome_usuario'])) {
    $queryParams['nome_usuario'] = $_GET['nome_usuario'];
}
if (!empty($_GET['cpf_usuario'])) {
    $queryParams['cpf_usuario'] = $_GET['cpf_usuario'];
}
if (!empty($_GET['telefone'])) {
    $queryParams['telefone'] = $_GET['telefone'];
}

$apiUrlWithFilters = $apiUrl . '?' . http_build_query($queryParams);

$response = callApi('GET', $apiUrlWithFilters);
$usuarios = $response['http_code'] === 200 ? $response['data'] : [];

// Preencher o formulário de edição
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
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-green-50">
<div class="container mx-auto mt-5 p-4">
    <h1 class="text-5xl 15px font-semibold text-green-700 mb-6">Gerenciar Usuários</h1>

    <?php if (!empty($message)): ?>
        <div class="alert <?= strpos($message, 'Erro') !== false ? 'alert-danger' : 'alert-success' ?>" role="alert">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <!-- Criação/Edição -->
    <form method="POST" class="space-y-4 bg-white p-6 rounded-lg shadow-lg">
        <input type="hidden" name="action" value="<?= $editData ? 'edit' : 'create' ?>">
        <?php if ($editData): ?>
            <input type="hidden" name="id_usuario" value="<?= $editData['id_usuario'] ?>">
        <?php endif; ?>
        <div class="mb-4">
            <label for="cpf_usuario" class="form-label">CPF</label>
            <input 
                type="text" 
                class="form-control" 
                id="cpf_usuario" 
                name="cpf_usuario" 
                value="<?= $editData['cpf_usuario'] ?? '' ?>" 
                pattern="\d{11}" 
                title="O CPF deve conter exatamente 11 dígitos numéricos." 
                required
            >
        </div>
        <div class="mb-4">
            <label for="nome_usuario" class="form-label">Nome</label>
            <input 
                type="text" 
                class="form-control" 
                id="nome_usuario" 
                name="nome_usuario" 
                value="<?= $editData['nome_usuario'] ?? '' ?>" 
                minlength="3" 
                title="O nome deve ter no mínimo 3 caracteres." 
                required
            >
        </div>
        <div class="mb-4">
            <label for="telefone" class="form-label">Telefone</label>
            <input 
                type="text" 
                class="form-control" 
                id="telefone" 
                name="telefone" 
                value="<?= $editData['telefone'] ?? '' ?>" 
                pattern="\d{11}" 
                title="O telefone deve conter exatamente 11 dígitos numéricos." 
                required
            >
        </div>
        <div class="mb-4">
            <label for="senha" class="form-label">Senha</label>
            <input 
                type="password" 
                class="form-control" 
                id="senha" 
                name="senha" 
                minlength="6" 
                title="A senha deve ter no mínimo 6 caracteres." 
                <?= $editData ? '' : 'required' ?>
            >
        </div>
        <button type="submit" class="btn text-white bg-green-600">Salvar</button>
    </form>


    <!-- Tabela de Listagem de Usuários -->
    <div class="mt-6 overflow-x-auto bg-white rounded-lg shadow-lg">
        <table class="min-w-full table-auto">
            <thead class="bg-green-600 text-white">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-medium">ID</th>
                    <th class="px-6 py-3 text-left text-sm font-medium">CPF</th>
                    <th class="px-6 py-3 text-left text-sm font-medium">Nome</th>
                    <th class="px-6 py-3 text-left text-sm font-medium">Telefone</th>
                    <th class="px-6 py-3 text-left text-sm font-medium">Ações</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                <?php if (!empty($usuarios)): ?>
                    <?php foreach ($usuarios as $usuario): ?>
                        <tr class="border-t hover:bg-green-50">
                            <td class="px-6 py-3"><?= htmlspecialchars($usuario['id_usuario']) ?></td>
                            <td class="px-6 py-3"><?= htmlspecialchars($usuario['cpf_usuario']) ?></td>
                            <td class="px-6 py-3"><?= htmlspecialchars($usuario['nome_usuario']) ?></td>
                            <td class="px-6 py-3"><?= htmlspecialchars($usuario['telefone']) ?></td>
                            <td class="px-6 py-3 flex space-x-2">
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="id_usuario" value="<?= htmlspecialchars($usuario['id_usuario']) ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <button type="submit" class="btn text-white bg-red-600 hover:bg-red-800 btn-sm">Excluir</button>
                                </form>
                                <a href="?edit_id=<?= htmlspecialchars($usuario['id_usuario']) ?>" class="btn text-white bg-yellow-400 hover:bg-yellow-600 btn-sm">Editar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center py-3 text-gray-500">Nenhum usuário encontrado.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <a href="index.html" class="btn text-white bg-green-600 m-3">Voltar</a>
</div>
</body>
</html>
