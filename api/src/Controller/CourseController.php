<?php
namespace Controller;

use Error\APIException;
use Http\Request;
use Service\CourseService;

class CourseController
{
   private CourseService $service;

   public function __construct()
   {
      $this->service = new CourseService();
   }

   public function processRequest(Request $request)
   {
      $id = $request->getId();
      $method = $request->getMethod();
      $subCollection = $request->getSubCollection();  //para o caso de /courses/id/students

      //Para as rotas que possuem id, tipo /courses/id
      if ($id) {
         //verifica se o id é numético
         if (!is_numeric($id)) throw new APIException("Course Id must be a number!", 400);

         switch ($method) { //conforme o método
            case "GET":
               if (!$subCollection) { 
                  //para o caso GET /courses/id
                  //busca o curso pelo id
                  $response = $this->service->getCourseById($id);
                  //retorna o curso encontrado no formato JSON
                  echo json_encode($response);
               } else  if($subCollection === "students") {
                  //para o caso GET /courses/id/students
                  //busca todos o estudante para o curso informado
                  $response = $this->service->getCourseStudents($id);
                  //retorna o conjunto de estudantes encontrados no formato JSON
                  echo json_encode($response);
               } else {
                  //demais subcollections não são válidas
                  throw new APIException("Resource not found!", 404);
               }
               break;
            case "PUT":
               //verifica se o corpo da requisição está correto
               $course = $this->validateBody($request->getBody());
               //atualiza os dados do curso
               $course["id"] = $id;
               $response = $this->service->updateCourse(...$course);
               //retorna o curso atualizado no formato JSON
               echo json_encode($response);
               break;
            case "DELETE":
               //exclui o curso especificado pelo id
               $this->service->deleteCourse($id);
               http_response_code(204);
               break;
            default:
               //para qualquer outro método, gera uma exceção
               throw new APIException("Method not allowed!",405);
         }
      } else {
         switch ($method) {
            case "GET":
               //obtem o parâmetro de busca da querystring (se houver)
               $name = $request->getQueryParams()["name"] ?? null;
               //busca o conjunto de cursos
               $response = $this->service->getCourses($name);
               //retorna o conjunto de cursos no formato JSON
               echo json_encode($response);
               break;
            case "POST":
               //verifica se o corpo da requisição está correto
               $course = $this->validateBody($request->getBody());
               //cria o novo curso
               $response = $this->service->createNewCourse(...$course);
               //retorna o curso criado no formato JSON
               http_response_code(201);
               echo json_encode($response);
               break;
            default:
               //para qualquer outro método, gera uma exceção
               throw new APIException("Method not allowed!",405);
         }
      }
   }

   private function validateBody(array $body): array {
      //verifica se o nome do curso foi informado
      if (!isset($body["name"])) throw new APIException("Property name is required!", 400);
      
      //verifica se o número de períodos do curso foi informado
      if (!isset($body["periods"])) throw new APIException("Property periods is required!", 400);
      
      //verifica se o número de períodos do curso é numérico
      if (!is_int($body["periods"])) throw new APIException("Property periods must be a number", 400);

      //cria um array com os dados do curso
      $course = [];
      $course["name"] = $body["name"];
      $course["periods"] = $body["periods"];

      //retorna o array criado
      return $course;
   }
}