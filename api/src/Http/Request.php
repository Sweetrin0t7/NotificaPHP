<?php

namespace Http;

use Error\APIException;

class Request {
   private string $resource;
   private ?string $id; //pode ser um strig ou null
   private ?string $subCollection; //pode ser um strig ou null
   private string $method; //o método (verbo) HTTP da requisição
   private array $body; //array com os parâmetros enviados via body
   private array $queryParams; //array com os parâmetros enviados via querystring

   //construtor
   function __construct() {
      //separa apenas o path da URL
      $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
      
      //cria uma expressão regular com um padrão que admite 
      //qualquer texto (para o início da URL) até encontrar /api/ 
      //tudo o que vier depois será capturado pela subexpressão
      //especificada por (.*) e referenciada por $1. 
      $pattern = "/.*\/api\/(.*)$/";
      
      //por exemplo, para http://localhost/xyz/api/students/123 
      //$route recebe students/123
      $route = preg_replace($pattern, "$1", $path);

      //cria um array com os segmentos de $route separados por /
      $segments = explode('/', $route);

      $this->resource = $segments[0]; //o primeiro segmento é o recurso
      $this->id = $segments[1] ?? null; //o segundo segmento é id (se não houver, nulo)
      $this->subCollection = $segments[2] ?? null; //o terceito segmento é a subcoleção (se não houver, nulo)
      
      //obtém o método (verbo) HTTP da requisição
      $this->method = $_SERVER["REQUEST_METHOD"];

      //cria um array com os parâmetros da querystring
      $this->queryParams = [];
      $queryString = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY); //pega só a querystring
      parse_str($queryString, $this->queryParams); //gera um array associativo

      //verifica o corpo da requisição
      $body = file_get_contents("php://input");
      if ($body) {
         //decodifica o corpo que deve vir no formato JSON
         //gera um array associativo
         $this->body = json_decode($body, true) ?? [];

         //caso não venha em JSON ou seja um JSON inválido, gera uma exceção
         if (json_last_error() !== JSON_ERROR_NONE) throw new APIException("Invalid request body!", 400);
      } else {
         //se não houver um corpo, devolve um array vazio
         $this->body = []; 
      }
   }

   //métodos GET
   public function getResource(): string {
      return $this->resource;
   }

   public function getId(): ?string {
      return $this->id;
   }

   public function getSubCollection(): ?string {
      return $this->subCollection;
   }

   public function getMethod(): string {
      return $this->method;
   }

   public function getQueryParams(): array {
      return $this->queryParams;
   }

   public function getBody(): array {
      return $this->body;
   }
}