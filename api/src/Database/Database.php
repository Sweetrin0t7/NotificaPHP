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
  
      $sql = "DROP TABLE IF EXISTS curtidas;
              DROP TABLE IF EXISTS comentarios;
              DROP TABLE IF EXISTS denuncias;
              DROP TABLE IF EXISTS usuarios;";
      $connection->exec($sql);
  
      $sql = "CREATE TABLE usuarios (
                 id_usuario INT NOT NULL AUTO_INCREMENT,
                 cpf_usuario VARCHAR(11) NOT NULL,
                 nome_usuario VARCHAR(50) NOT NULL,
                 telefone VARCHAR(15) NULL,
                 senha VARCHAR(255) NOT NULL,
                 data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                 tipo_usuario ENUM('comum', 'admin') NOT NULL,
                 PRIMARY KEY (id_usuario)
              ) ENGINE = InnoDB;";
      $connection->exec($sql);
  
      $sql = "CREATE TABLE denuncias (
                 id_denuncias INT NOT NULL AUTO_INCREMENT,
                 titulo VARCHAR(100) NOT NULL,
                 descricao VARCHAR(250) NOT NULL,
                 categoria ENUM('agua', 'saneamento', 'obras', 'outros') NOT NULL,
                 imagem VARCHAR(255) NULL,
                 localizacao VARCHAR(255) NULL,
                 status ENUM('pendente', 'em andamento', 'resolvido') NOT NULL,
                 anonimo TINYINT NOT NULL DEFAULT 0,
                 data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                 Usuarios_id_usuario INT NOT NULL,
                 PRIMARY KEY (id_denuncias),
                 FOREIGN KEY (Usuarios_id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE
              ) ENGINE = InnoDB;";
      $connection->exec($sql);
  
      $sql = "CREATE TABLE comentarios (
                 id_comentario INT NOT NULL AUTO_INCREMENT,
                 conteudo VARCHAR(255) NOT NULL,
                 data_comentario TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                 Denuncias_id_denuncias INT NOT NULL,
                 Usuarios_id_usuario INT NOT NULL,
                 PRIMARY KEY (id_comentario),
                 FOREIGN KEY (Denuncias_id_denuncias) REFERENCES denuncias(id_denuncias) ON DELETE CASCADE,
                 FOREIGN KEY (Usuarios_id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE
              ) ENGINE = InnoDB;";
      $connection->exec($sql);
  
      $sql = "CREATE TABLE curtidas (
                 id_curtida INT NOT NULL AUTO_INCREMENT,
                 id_usuario INT NOT NULL,
                 id_denuncia INT NOT NULL,
                 PRIMARY KEY (id_curtida),
                 FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
                 FOREIGN KEY (id_denuncia) REFERENCES denuncias(id_denuncias) ON DELETE CASCADE
              ) ENGINE = InnoDB;";
      $connection->exec($sql);
  
      $usuarios = [
          ["cpf_usuario" => "12345678901", "nome_usuario" => "Ana Silva", "telefone" => "11987654321", "senha" => "senha123", "tipo_usuario" => "comum"],
          ["cpf_usuario" => "09876543210", "nome_usuario" => "Bruno Costa", "telefone" => "21987654321", "senha" => "senha456", "tipo_usuario" => "admin"],
          ["cpf_usuario" => "11223344556", "nome_usuario" => "Carlos Souza", "telefone" => "31987654321", "senha" => "senha789", "tipo_usuario" => "comum"],
      ];
  
      $stmt = $connection->prepare("INSERT INTO usuarios (cpf_usuario, nome_usuario, telefone, senha, tipo_usuario) 
                                    VALUES (:cpf_usuario, :nome_usuario, :telefone, :senha, :tipo_usuario);");
      foreach ($usuarios as $usuario) {
          $stmt->execute($usuario);
      }

      $denuncias = [
          ["titulo" => "Falta de água", "descricao" => "Não há fornecimento de água no bairro.", "categoria" => "agua", "imagem" => "imagem1.jpg", "localizacao" => "Rua A, Bairro X", "status" => "pendente", "anonimo" => 0, "Usuarios_id_usuario" => 1],
          ["titulo" => "Buraco na rua", "descricao" => "Um grande buraco está atrapalhando o trânsito.", "categoria" => "obras", "imagem" => "imagem2.jpg", "localizacao" => "Rua B, Bairro Y", "status" => "em andamento", "anonimo" => 1, "Usuarios_id_usuario" => 2],
      ];
  
      $stmt = $connection->prepare("INSERT INTO denuncias (titulo, descricao, categoria, imagem, localizacao, status, anonimo, Usuarios_id_usuario) 
                                    VALUES (:titulo, :descricao, :categoria, :imagem, :localizacao, :status, :anonimo, :Usuarios_id_usuario);");
      foreach ($denuncias as $denuncia) {
          $stmt->execute($denuncia);
      }
  
      $comentarios = [
          ["conteudo" => "Também estamos sem água por aqui.", "Denuncias_id_denuncias" => 1, "Usuarios_id_usuario" => 3],
          ["conteudo" => "Isso precisa ser resolvido logo!", "Denuncias_id_denuncias" => 2, "Usuarios_id_usuario" => 1],
      ];
  
      $stmt = $connection->prepare("INSERT INTO comentarios (conteudo, Denuncias_id_denuncias, Usuarios_id_usuario) 
                                    VALUES (:conteudo, :Denuncias_id_denuncias, :Usuarios_id_usuario);");
      foreach ($comentarios as $comentario) {
          $stmt->execute($comentario);
      }

      $curtidas = [
          ["id_usuario" => 1, "id_denuncia" => 1],
          ["id_usuario" => 2, "id_denuncia" => 2],
      ];
  
      $stmt = $connection->prepare("INSERT INTO curtidas (id_usuario, id_denuncia) 
                                    VALUES (:id_usuario, :id_denuncia);");
      foreach ($curtidas as $curtida) {
          $stmt->execute($curtida);
      }
  
      http_response_code(201);
      echo json_encode(["message" => "Database is ready!"]);
  }
  
}
