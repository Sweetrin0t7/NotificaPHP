<?php

require_once 'src/config.php';
require_once 'src/Apresentacao.php';

use Api\Apresentacao;  
use Controller\UsuarioController;
use Controller\DenunciaController;
use Http\Request;
use Database\Database;
use Error\APIException;

header("Content-Type: application/json");

$request = new Request();  

if (isset($_SERVER['REQUEST_URI']) && ($_SERVER['REQUEST_URI'] === '/api' || $_SERVER['REQUEST_URI'] === '/api/')) {
    Apresentacao::apresentacao();  
    exit;  
} else {
    switch ($request->getResource()) { 
        case 'students':
            $studentsController = new StudentController();
            $studentsController->processRequest($request);
            break;

        case 'denuncias':
            $controller = new DenunciaController();
            $controller->processRequest($request);
        break;
            
        case 'config': 
            Database::config();
            break;

        case 'db': 
            if ($_SERVER['REQUEST_METHOD'] == 'GET') {
                Database::config();  
            } else {
                header("HTTP/1.1 405 Method Not Allowed");
                echo json_encode(["error" => "Método não permitido para esta rota."]);
            }
            break;

        default:
            //*Tratar rotas inexistentes e métodos não permitidos
            throw new APIException("Recurso não encontrado!", 404);
    }
}