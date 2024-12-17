<?php
require_once __DIR__ . '/../api/libs/dompdf/autoload.inc.php';
use Dompdf\Dompdf;

class DenunciaPDF {
    public static function gerarPDFDenuncias($denuncias) {  
        // Inicializa o Dompdf
        $dompdf = new Dompdf();

        // Cria o conteúdo HTML para o PDF (personalize conforme necessário)
        $html = '<html><body><h1>Denúncias</h1><ul>';
        foreach ($denuncias as $denuncia) {
            $html .= '<li>' . htmlspecialchars($denuncia->getDescricao()) . '</li>';
        }
        $html .= '</ul></body></html>';

        // Carregar o conteúdo HTML no Dompdf
        $dompdf->loadHtml($html);

        // Renderizar o PDF
        $dompdf->render();

        // Enviar o PDF para o navegador (pode ser alterado para download ou stream)
        $dompdf->stream('denuncias.pdf');
    }
}

