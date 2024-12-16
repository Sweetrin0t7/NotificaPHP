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

      // Exclui as tabelas existentes (para testes)
      $sql = "DROP TABLE IF EXISTS curtidas;
              DROP TABLE IF EXISTS comentarios;
              DROP TABLE IF EXISTS denuncias;
              DROP TABLE IF EXISTS usuarios;";
      $connection->exec($sql);

      // Criação das tabelas
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
                 imagem LONGBLOB NULL,  
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

      // Inserir alguns usuários para testar
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

      // Imagem Base64
      $imagemBase64agua = 'data:image/jpeg;base64,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';

      $imagemBase64buraco = 'data:image/jpeg;base64,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';

      // Inserir uma denúncia com imagem em Base64
      $denuncias = [
          ["titulo" => "Falta de água", "descricao" => "Não há fornecimento de água no bairro.", "categoria" => "agua", "imagem" => $imagemBase64agua, "localizacao" => "Rua A, Bairro X", "status" => "pendente", "anonimo" => 0, "Usuarios_id_usuario" => 1],
          ["titulo" => "Buraco na rua", "descricao" => "Um grande buraco está atrapalhando o trânsito.", "categoria" => "obras", "imagem" => $imagemBase64buraco, "localizacao" => "Rua B, Bairro Y", "status" => "em andamento", "anonimo" => 1, "Usuarios_id_usuario" => 2],
      ];

      $stmt = $connection->prepare("INSERT INTO denuncias (titulo, descricao, categoria, imagem, localizacao, status, anonimo, Usuarios_id_usuario) 
                                    VALUES (:titulo, :descricao, :categoria, :imagem, :localizacao, :status, :anonimo, :Usuarios_id_usuario);");
      $stmt = $connection->prepare("INSERT INTO denuncias (titulo, descricao, categoria, imagem, localizacao, status, anonimo, Usuarios_id_usuario) 
      VALUES (:titulo, :descricao, :categoria, :imagem, :localizacao, :status, :anonimo, :Usuarios_id_usuario);");

        foreach ($denuncias as $denuncia) {
        $stmt->bindParam(':titulo', $denuncia['titulo']);
        $stmt->bindParam(':descricao', $denuncia['descricao']);
        $stmt->bindParam(':categoria', $denuncia['categoria']);
        $stmt->bindParam(':imagem', $denuncia['imagem']);
        $stmt->bindParam(':localizacao', $denuncia['localizacao']);
        $stmt->bindParam(':status', $denuncia['status']);
        $stmt->bindParam(':anonimo', $denuncia['anonimo']);
        $stmt->bindParam(':Usuarios_id_usuario', $denuncia['Usuarios_id_usuario']);

        $stmt->execute();

}

    http_response_code(201);
    echo json_encode(["message" => "Database is ready!"]);
   }
}

