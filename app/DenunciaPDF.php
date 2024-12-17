<?php
// *Deve ter pelo menos uma funcionalidade envolvendo arquivos PDF. Por exemplo, exportar uma listagem, salvar uma nota fiscal, etc.

require_once __DIR__ . '/libs/dompdf-3.0.1/autoload.inc.php';

use Dompdf\Dompdf;

function callApi(string $method, string $url, array $data = []): array {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return ['data' => json_decode($response, true), 'http_code' => $httpCode];
}

$apiUrl = "http://localhost/api/denuncias";
$response = callApi('GET', $apiUrl);
$denuncias = $response['http_code'] === 200 ? $response['data'] : [];

$html = '<h1 style="text-align: center;">Relatório de Denúncias</h1>';
$html .= '<table border="1" style="width: 100%; border-collapse: collapse; text-align: left;">';
$html .= '<thead>
            <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Categoria</th>
                <th>Status</th>
                <th>Descrição</th>
                <th>Localização</th>
            </tr>
          </thead>';
$html .= '<tbody>';

if (!empty($denuncias)) {
    foreach ($denuncias as $denuncia) {
        $html .= '<tr>
                    <td>' . htmlspecialchars($denuncia['id_denuncias']) . '</td>
                    <td>' . htmlspecialchars($denuncia['titulo']) . '</td>
                    <td>' . htmlspecialchars($denuncia['categoria']) . '</td>
                    <td>' . htmlspecialchars($denuncia['status']) . '</td>
                    <td>' . htmlspecialchars($denuncia['descricao']) . '</td>
                    <td>' . htmlspecialchars($denuncia['localizacao'] ?? 'Não informado') . '</td>
                  </tr>';
    }
} else {
    $html .= '<tr><td colspan="6" style="text-align: center;">Nenhuma denúncia encontrada.</td></tr>';
}

$html .= '</tbody></table>';

$dompdf = new Dompdf();
$dompdf->loadHtml($html);

$dompdf->setPaper('A4', 'landscape');
$dompdf->render();

$dompdf->stream("Relatorio_Denuncias.pdf", ["Attachment" => 1]);
exit;
?>
