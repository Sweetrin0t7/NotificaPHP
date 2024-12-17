<?php

namespace Controller;

use Error\APIException;
use Http\Request;
use Service\UsuarioService;
use Repository\UsuarioRepository;

class UsuarioController
{
    private UsuarioService $service;

    public function __construct()
    {
        $repository = new UsuarioRepository();
        $this->service = new UsuarioService($repository);
    }

    public function processRequest(Request $request)
    {
        $id = $request->getId();
        $method = $request->getMethod();

        if ($id) {
            if (!is_numeric($id)) throw new APIException("Usuario Id must be a number!", 400);

            switch ($method) {
                case "GET":
                    $response = $this->service->getUsuarioById($id);
                    echo json_encode($response);
                    break;
                case "PUT":
                    $usuario = $this->validateBody($request->getBody());
                    $usuario["id"] = $id;
                    $response = $this->service->updateUsuario(...$usuario);
                    echo json_encode($response);
                    break;
                case "DELETE":
                    $this->service->deleteUsuario($id);
                    http_response_code(204);
                    break;
                default:
                    throw new APIException("Method not allowed!", 405);
            }
        } else {
            switch ($method) {
                case "GET":
                    $nome_usuario = $request->getQueryParams()["nome_usuario"] ?? null;
                    $response = $this->service->getUsuarios($nome_usuario);
                    echo json_encode($response);
                    break;
                case "POST":
                    $usuario = $this->validateBody($request->getBody());
                    $response = $this->service->createNewUsuario(...$usuario);
                    http_response_code(201);
                    echo json_encode($response);
                    break;
                default:
                    throw new APIException("Method not allowed!", 405);
            }
        }
    }

    private function validateBody(array $body): array
    {
        error_log("Conteúdo do corpo da requisição: " . json_encode($body));
        
        if (!isset($body["cpf_usuario"])) throw new APIException("Propriedade 'cpf_usuario' é necessária!", 400);
        if (!isset($body["nome_usuario"])) throw new APIException("Propriedade 'nome_usuario' é necessária!!", 400);
        if (!isset($body["telefone"])) throw new APIException("Propriedade 'telefone' é necessária!!", 400);
        if (!isset($body["senha"])) throw new APIException("Propriedade 'senha' é necessária!!", 400);

        return [
            "cpf_usuario" => $body["cpf_usuario"],
            "nome_usuario" => $body["nome_usuario"],
            "telefone" => $body["telefone"],
            "senha" => $body["senha"]
        ];
    }
}
