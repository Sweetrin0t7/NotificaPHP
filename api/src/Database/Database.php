<?php

namespace Database;

class Database {
   //*O banco deve se chamar api, acessado pelo usuário root, sem senha, em localhost, na porta padrão.
   private static $host = 'localhost';
   private static $username = 'root';
   private static $password = '';
   private static $database = 'api';

   //*Deve utilizar PDO para a interação com o banco de dados;
   public static function getConnection(): \PDO {
      $dsn = "mysql:host=" . self::$host . ";dbname=" . self::$database;
      
      $connection = new \PDO($dsn, self::$username, self::$password);
      
      $connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
      
      return $connection;
   }

   public static function config() {
      $connection = self::getConnection();
  
      // Exclui as tabelas se elas já existirem
      $sql = "DROP TABLE IF EXISTS usuarios;
              DROP TABLE IF EXISTS denuncias;
              DROP TABLE IF EXISTS comentarios;
              DROP TABLE IF EXISTS curtidas;";
      $connection->exec($sql);
  
      // Cria a tabela 'usuarios'
      $sql = "CREATE TABLE usuarios (
                 id_usuario INT NOT NULL,
                 cpf_usuario VARCHAR(11) NULL,
                 nome_usuario VARCHAR(50) NULL,
                 telefone VARCHAR(15) NULL,
                 senha VARCHAR(255) NULL,
                 data_cadastro TIMESTAMP NULL,
                 tipo_usuario ENUM('comum', 'admin') NULL,
                 Usuarioscol VARCHAR(45) NULL,
                 PRIMARY KEY (id_usuario)
              ) ENGINE = InnoDB;";
      $connection->exec($sql);
  
      // Cria a tabela 'denuncias'
      $sql = "CREATE TABLE denuncias (
                 id_denuncias INT NOT NULL,
                 titulo VARCHAR(100) NULL,
                 descricao VARCHAR(250) NULL,
                 categoria ENUM('agua', 'saneamento', 'obras', 'outros') NULL,
                 imagem VARCHAR(255) NULL,
                 localizacao VARCHAR(255) NULL,
                 status ENUM('pendente', 'em andamento', 'resolvido') NULL,
                 anonimo TINYINT NULL,
                 data_criacao TIMESTAMP NULL,
                 Usuarios_id_usuario INT NOT NULL,
                 PRIMARY KEY (id_denuncias),
                 FOREIGN KEY (Usuarios_id_usuario) REFERENCES usuarios(id_usuario)
              ) ENGINE = InnoDB;";
      $connection->exec($sql);
  
      // Cria a tabela 'comentarios'
      $sql = "CREATE TABLE comentarios (
                 id_comentario INT NOT NULL,
                 conteudo VARCHAR(255) NULL,
                 data_comentario TIMESTAMP NULL,
                 Denuncias_id_denuncias INT NOT NULL,
                 Usuarios_id_usuario INT NOT NULL,
                 PRIMARY KEY (id_comentario),
                 FOREIGN KEY (Denuncias_id_denuncias) REFERENCES denuncias(id_denuncias),
                 FOREIGN KEY (Usuarios_id_usuario) REFERENCES usuarios(id_usuario)
              ) ENGINE = InnoDB;";
      $connection->exec($sql);
  
      // Cria a tabela 'curtidas'
      $sql = "CREATE TABLE curtidas (
                 id_curtida INT NOT NULL,
                 id_usuario INT NOT NULL,
                 id_denuncia INT NULL,
                 PRIMARY KEY (id_curtida),
                 FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario),
                 FOREIGN KEY (id_denuncia) REFERENCES denuncias(id_denuncias)
              ) ENGINE = InnoDB;";
      $connection->exec($sql);
  
      // Inserção de dados mockados na tabela 'usuarios'
      $usuarios = [
          ["id_usuario" => 1, "cpf_usuario" => "12345678901", "nome_usuario" => "Ana Silva", "telefone" => "11987654321", "senha" => "senha123", "data_cadastro" => date("Y-m-d H:i:s"), "tipo_usuario" => "comum", "Usuarioscol" => "Extra1"],
          ["id_usuario" => 2, "cpf_usuario" => "09876543210", "nome_usuario" => "Bruno Costa", "telefone" => "21987654321", "senha" => "senha456", "data_cadastro" => date("Y-m-d H:i:s"), "tipo_usuario" => "admin", "Usuarioscol" => "Extra2"],
          ["id_usuario" => 3, "cpf_usuario" => "11223344556", "nome_usuario" => "Carlos Souza", "telefone" => "31987654321", "senha" => "senha789", "data_cadastro" => date("Y-m-d H:i:s"), "tipo_usuario" => "comum", "Usuarioscol" => "Extra3"],
      ];
  
      $stmt = $connection->prepare("INSERT INTO usuarios (id_usuario, cpf_usuario, nome_usuario, telefone, senha, data_cadastro, tipo_usuario, Usuarioscol) 
                                    VALUES (:id_usuario, :cpf_usuario, :nome_usuario, :telefone, :senha, :data_cadastro, :tipo_usuario, :Usuarioscol);");
      foreach ($usuarios as $usuario) {
          $stmt->execute($usuario);
      }
  
      // Inserção de dados mockados na tabela 'denuncias'
      $denuncias = [
          ["id_denuncias" => 1, "titulo" => "Falta de água", "descricao" => "Não há fornecimento de água no bairro.", "categoria" => "agua", "imagem" => "imagem1.jpg", "localizacao" => "Rua A, Bairro X", "status" => "pendente", "anonimo" => 0, "data_criacao" => date("Y-m-d H:i:s"), "Usuarios_id_usuario" => 1],
          ["id_denuncias" => 2, "titulo" => "Buraco na rua", "descricao" => "Um grande buraco está atrapalhando o trânsito.", "categoria" => "obras", "imagem" => "imagem2.jpg", "localizacao" => "Rua B, Bairro Y", "status" => "em andamento", "anonimo" => 1, "data_criacao" => date("Y-m-d H:i:s"), "Usuarios_id_usuario" => 2],
      ];
  
      $stmt = $connection->prepare("INSERT INTO denuncias (id_denuncias, titulo, descricao, categoria, imagem, localizacao, status, anonimo, data_criacao, Usuarios_id_usuario) 
                                    VALUES (:id_denuncias, :titulo, :descricao, :categoria, :imagem, :localizacao, :status, :anonimo, :data_criacao, :Usuarios_id_usuario);");
      foreach ($denuncias as $denuncia) {
          $stmt->execute($denuncia);
      }
  
      // Inserção de dados mockados na tabela 'comentarios'
      $comentarios = [
          ["id_comentario" => 1, "conteudo" => "Também estamos sem água por aqui.", "data_comentario" => date("Y-m-d H:i:s"), "Denuncias_id_denuncias" => 1, "Usuarios_id_usuario" => 3],
          ["id_comentario" => 2, "conteudo" => "Isso precisa ser resolvido logo!", "data_comentario" => date("Y-m-d H:i:s"), "Denuncias_id_denuncias" => 2, "Usuarios_id_usuario" => 1],
      ];
  
      $stmt = $connection->prepare("INSERT INTO comentarios (id_comentario, conteudo, data_comentario, Denuncias_id_denuncias, Usuarios_id_usuario) 
                                    VALUES (:id_comentario, :conteudo, :data_comentario, :Denuncias_id_denuncias, :Usuarios_id_usuario);");
      foreach ($comentarios as $comentario) {
          $stmt->execute($comentario);
      }
  
      // Inserção de dados mockados na tabela 'curtidas'
      $curtidas = [
          ["id_curtida" => 1, "id_usuario" => 1, "id_denuncia" => 1],
          ["id_curtida" => 2, "id_usuario" => 2, "id_denuncia" => 2],
      ];
  
      $stmt = $connection->prepare("INSERT INTO curtidas (id_curtida, id_usuario, id_denuncia) 
                                    VALUES (:id_curtida, :id_usuario, :id_denuncia);");
      foreach ($curtidas as $curtida) {
          $stmt->execute($curtida);
      }
  
      // Retorna uma resposta indicando o sucesso da operação
      http_response_code(201);
      echo json_encode(["message" => "Database is ready!"]);
  }
  
}