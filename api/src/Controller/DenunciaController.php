<?php
namespace Controller;

use Error\APIException;
use Http\Request;
use Service\DenunciaService;

class DenunciaController
{
    private DenunciaService $service;

    public function __construct()
    {
        $this->service = new DenunciaService();
    }

    public function processRequest(Request $request)
    {
        $id = $request->getId();
        $method = $request->getMethod();

        if ($id) {
            if (!is_numeric($id)) throw new APIException("Denuncia Id precisar ser um número!", 400);

            switch ($method) {
                case "GET":
                    $response = $this->service->getDenunciaById($id);
                    echo json_encode($response);
                    break;
                case "PUT":
                    $denuncia = $this->validateBody($request->getBody());
                    $denuncia["id"] = $id;
                    $response = $this->service->updateDenuncia(...$denuncia);
                    echo json_encode($response);
                    break;
                case "DELETE":
                    $this->service->deleteDenuncia($id);
                    http_response_code(204);
                    break;
                default:
                    //*Para qualquer outro método, gera uma exceção
                    throw new APIException("Method not allowed!", 405);
            }
        } else {
            switch ($method) {
                case "GET":
                    // Obtém parâmetros de busca da querystring (se houver)
                    $titulo = $request->getQueryParams()["titulo"] ?? null;
                    $status = $request->getQueryParams()["status"] ?? null;

                    $response = $this->service->getDenuncias($titulo, $status);
                    echo json_encode($response);
                    break;
                case "POST":
                    $denuncia = $this->validateBody($request->getBody());
                    $response = $this->service->createNewDenuncia(...$denuncia);
                    http_response_code(201);
                    echo json_encode($response);
                    break;
                default:
                    //*Para qualquer outro método, gera uma exceção
                    throw new APIException("Method not allowed!", 405);
            }
        }
    }

    private function validateBody(array $body): array
    {
        // Verifica se o título foi informado
        if (!isset($body["titulo"])) throw new APIException("Propriedade 'titulo' é necessária!", 400);

        // Verifica se a descrição foi informada
        if (!isset($body["descricao"])) throw new APIException("Propriedade 'descricao' é necessária!", 400);

        // Verifica se a categoria foi informada
        if (!isset($body["categoria"])) throw new APIException("Propriedade 'categoria' é necessária!", 400);

        // Verifica se o status foi informado
        if (!isset($body["status"])) throw new APIException("Propriedade 'status' é necessária!", 400);

        // Verifica se o ID do usuário foi informado
        if (!isset($body["Usuarios_id_usuario"])) throw new APIException("Propriedade 'Usuarios_id_usuario' é necessária!", 400);

        // Cria um array com os dados da denúncia
        $denuncia = [
            "titulo" => $body["titulo"],
            "descricao" => $body["descricao"],
            "categoria" => $body["categoria"],
            "status" => $body["status"],
            "Usuarios_id_usuario" => (int) $body["Usuarios_id_usuario"],
            "anonimo" => $body["anonimo"] ?? false,
            "imagem" => $body["imagem"] ?? null,
            "localizacao" => $body["localizacao"] ?? null
        ];

        return $denuncia;
    }
}
