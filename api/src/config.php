<?php

function autoload(string $className){
   //$classname possui tanto o namespace quanto o nome da classe
   //exemplo: Model\Student
   //assim precisa trocar \ por / para formar o caminho ..../Model/Student.php
   $className = str_replace('\\', '/', $className);
   
   //define o caminho para o arquivo
   $file = __DIR__ . '/' . $className . '.php';

   //se existir, inclui o arquivo
   if (file_exists($file)) {
      require_once $file;
   }
}

//registra a função autoload para ser responsável por carregar
//todos os arquivos das classes que forem sendo utilizadas 
spl_autoload_register('autoload');

use Error\APIException;

function exceptionHandler(\Throwable $exception)
{
   //$exception é objeto da classe Throwable
   //assim, adimite objetos da classe Error e Exception, que herdam de Throwable
   //bem como objetos da classe APIException, que herda de Exception

   //objetos da classe APIException chegarão aqui com mensagens de erro
   //e códigos personalizados, que nós programamos, pois eram previstos

   //demais objetos chegarão com mensagens e códigos do próprio PHP,
   //por isso devemos alterá-lo antes de encaminhar a resposta

   if ($exception instanceof APIException) {
      //Para as exceções previstas e geradas na própria API
      http_response_code($exception->getCode());
      echo json_encode(['message' => $exception->getMessage()]);
   } else {
      //Para as exceções não previstas, geradas pelo PHP
      http_response_code(500);
      print_r($exception); //apenas para testes e debug
      echo json_encode(['message' => 'Unable to process this request!']);
   }
}

//registra a função exceptionHandler() para ser responsável por tratar
//todas as exceções e erros não capturados
set_exception_handler('exceptionHandler');
