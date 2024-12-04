<?php
require_once 'src/config.php';

use Controller\StudentController;
use Controller\CourseController;
use Http\Request;
use Database\Database;
use Error\APIException;

header("Content-Type: application/json"); 

$request = new Request();

switch ($request->getResource()) { 
   case 'students':
      $studentsController = new StudentController();
      $studentsController->processRequest($request);
      break;
   case 'courses':
      $coursesController = new CourseController();
      $coursesController->processRequest($request);
      break;
   case 'config': 
      Database::config();
      break;
   case null: 
      $routes = [
         "GET /api/students",
         "POST /api/students",
         "GET /api/sutdentes/:id",
         "PUT /api/students/:id",
         "PATCH /api/students/:id",
         "DELETE /api/students/:id",
         "GET /api/courses",
         "POST /api/courses",
         "GET /api/courses/:id",
         "PUT /api/courses/:id",
         "DELETE /api/courses/:id",
         "GET /api/courses/:id/students",
         //Deve implementar a rota / que retorna uma apresentação da API, com a identificação
         //do(s) autor(es) e a lista de todas as rotas (caminho e método) disponíveis, também em
         //formato JSON: { “autores”: [...], “rotas”: [...];
         //Deve implementar uma rota /api/db que cria a(s) tabela(s) e um conjunto de dados de exemplo no banco de dados.
      ];
      echo json_encode(['routes' => $routes], JSON_UNESCAPED_SLASHES);
      break;
   default:
      //Tratar rotas inexistes e métodos não permitidos
      throw new APIException("Resource not found!", 404);
}