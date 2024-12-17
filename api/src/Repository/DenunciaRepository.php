<?php

namespace Repository;

use Database\Database;
use Error\APIException;
use Model\Denuncia;

class DenunciaRepository {
   private $connection;

   public function __construct() {
      $this->connection = Database::getConnection();
   }

   public function findAll(): array {
      $stmt = $this->connection->prepare("SELECT * FROM denuncias");
      $stmt->execute();
      
      $denuncias = [];
      while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
         $denuncia = new Denuncia(
            id_denuncias: $row['id_denuncias'],
            titulo: $row['titulo'],
            descricao: $row['descricao'],
            categoria: $row['categoria'],
            status: $row['status'],
            anonimo: (bool) $row['anonimo'],
            data_criacao: $row['data_criacao'],
            imagem: $row['imagem'],
            localizacao: $row['localizacao'],
            Usuarios_id_usuario: $row['Usuarios_id_usuario']
         );
         $denuncias[] = $denuncia;
      }

      return $denuncias;
   }

   public function findByTitulo(string $titulo): array {
      $stmt = $this->connection->prepare("SELECT * FROM denuncias 
                                          WHERE titulo LIKE :titulo");
      $stmt->bindValue(':titulo', '%' . $titulo . '%');
      $stmt->execute();

      $denuncias = [];
      while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
         $denuncia = new Denuncia(
            id_denuncias: $row['id_denuncias'],
            titulo: $row['titulo'],
            descricao: $row['descricao'],
            categoria: $row['categoria'],
            status: $row['status'],
            anonimo: (bool) $row['anonimo'],
            imagem: $row['imagem'],
            data_criacao: $row['data_criacao'],
            localizacao: $row['localizacao'],
            Usuarios_id_usuario: $row['Usuarios_id_usuario']
         );
         $denuncias[] = $denuncia;
      }

      return $denuncias;
   }

   public function findById(int $id_denuncias): ?Denuncia {
      $stmt = $this->connection->prepare("SELECT * FROM denuncias 
                                          WHERE id_denuncias = :id_denuncias");
      $stmt->bindValue(':id_denuncias', $id_denuncias, \PDO::PARAM_INT);
      $stmt->execute();

      $row = $stmt->fetch(\PDO::FETCH_ASSOC);
      if (!$row) return null;

      $denuncia = new Denuncia(
         id_denuncias: $row['id_denuncias'],
         titulo: $row['titulo'],
         descricao: $row['descricao'],
         categoria: $row['categoria'],
         status: $row['status'],
         anonimo: (bool) $row['anonimo'],
         imagem: $row['imagem'],
         localizacao: $row['localizacao'],
         Usuarios_id_usuario: $row['Usuarios_id_usuario']
      );

      return $denuncia;
   }

   public function create(Denuncia $denuncia): Denuncia {
      $stmt = $this->connection->prepare("INSERT INTO denuncias (titulo, descricao, categoria, status, anonimo, imagem, localizacao, Usuarios_id_usuario) 
                                          VALUES (:titulo, :descricao, :categoria, :status, :anonimo, :imagem, :localizacao, :Usuarios_id_usuario)");
      $stmt->bindValue(':titulo', $denuncia->getTitulo());
      $stmt->bindValue(':descricao', $denuncia->getDescricao());
      $stmt->bindValue(':categoria', $denuncia->getCategoria());
      $stmt->bindValue(':status', $denuncia->getStatus());
      $stmt->bindValue(':anonimo', $denuncia->isAnonimo());
      $stmt->bindValue(':imagem', $denuncia->getImagem(), \PDO::PARAM_STR);
      $stmt->bindValue(':localizacao', $denuncia->getLocalizacao());
      $stmt->bindValue(':Usuarios_id_usuario', $denuncia->getUsuariosIdUsuario(), \PDO::PARAM_INT);
      $stmt->execute();

      // Recupera o id gerado pelo banco
      $denuncia->setIdDenuncias($this->connection->lastInsertId());

      return $denuncia;
   }

   public function update(Denuncia $denuncia) {
      $stmt = $this->connection->prepare("UPDATE denuncias SET 
                                             titulo = :titulo, 
                                             descricao = :descricao, 
                                             categoria = :categoria, 
                                             status = :status, 
                                             anonimo = :anonimo, 
                                             imagem = :imagem, 
                                             localizacao = :localizacao, 
                                             Usuarios_id_usuario = :Usuarios_id_usuario
                                             WHERE id_denuncias = :id_denuncias;");
      $stmt->bindValue(':id_denuncias', $denuncia->getIdDenuncias(), \PDO::PARAM_INT);                                           
      $stmt->bindValue(':titulo', $denuncia->getTitulo(), \PDO::PARAM_STR);
      $stmt->bindValue(':descricao', $denuncia->getDescricao(), \PDO::PARAM_STR);
      $stmt->bindValue(':categoria', $denuncia->getCategoria(), \PDO::PARAM_STR);
      $stmt->bindValue(':status', $denuncia->getStatus(), \PDO::PARAM_STR);
      $stmt->bindValue(':anonimo', $denuncia->isAnonimo(), \PDO::PARAM_BOOL);
      $stmt->bindValue(':imagem', $denuncia->getImagem(), \PDO::PARAM_STR);
      $stmt->bindValue(':localizacao', $denuncia->getLocalizacao());
      $stmt->bindValue(':Usuarios_id_usuario', $denuncia->getUsuariosIdUsuario(), \PDO::PARAM_INT);
      $stmt->execute();
   }

   public function delete(int $id_denuncias) {
      $stmt = $this->connection->prepare("DELETE FROM denuncias 
                                          WHERE id_denuncias = :id_denuncias;");
      $stmt->bindValue(':id_denuncias', $id_denuncias, \PDO::PARAM_INT);                                           
      $stmt->execute();
   }
}
