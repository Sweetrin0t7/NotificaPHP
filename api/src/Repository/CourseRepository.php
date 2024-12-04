<?php

namespace Repository;

use Database\Database;
use Error\APIException;
use Model\Course;

class CourseRepository {
   private $connection;

   public function __construct() {
      //estabelece uma conexão
      $this->connection = Database::getConnection();
   }

   public function findAll(): array {
      //executa a consulta no banco
      $stmt = $this->connection->prepare("SELECT * FROM courses");
      $stmt->execute();
      
      //para cada linha de retorno, cria um objeto Curso
      //e aramazena em um array
      $courses = [];
      while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
         $course = new Course(
            id: $row['id'],
            name: $row['name'],
            periods: $row['periods'],
         );
         $courses[] = $course;
      }

      //retorna o conjunto de cursos encontrado
      return $courses;
   }

   public function findByName(string $name): array {
      //executa a consulta no banco
      $stmt = $this->connection->prepare("SELECT * FROM courses 
                                          WHERE name like :name");
      $stmt->bindValue(':name', '%' . $name . '%');
      $stmt->execute();

      //para cada linha de retorno, cria um objeto Student
      //e aramazena em um array
      $courses = [];
      while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
         $course = new Course(
            id: $row['id'],
            name: $row['name'],
            periods: $row['periods'],
         );
         $courses[] = $course;
      }

      //retorna o conjunto de cursos encontrado
      return $courses;
   }

   public function findById(int $id): ?Course {
      //executa a consulta no banco
      $stmt = $this->connection->prepare("SELECT * FROM courses 
                                          WHERE id = :id");
      $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
      $stmt->execute();

      //se não achou, retorna nulo
      $row = $stmt->fetch(\PDO::FETCH_ASSOC);
      if (!$row) return null;

      //se achou, cria um objeto Course
      $course = new Course(
         id: $row['id'],
         name: $row['name'],
         periods: $row['periods'],
      );

      //retorna o curso encontrado
      return $course;
   }

   public function create(Course $course): Course {  
      //executa a operação no banco    
      $stmt = $this->connection->prepare("INSERT INTO courses (name, periods) 
                                          VALUES (:name, :periods)");
      $stmt->bindValue(':name', $course->getName());
      $stmt->bindValue(':periods', $course->getPeriods(), \PDO::PARAM_INT);
      $stmt->execute();

      //recupera o id gerado pelo banco
      $course->setId($this->connection->lastInsertId());

      //retorna o curso criado
      return $course;
   }

   public function update(Course $course) {
      //executa a operação no banco
      $stmt = $this->connection->prepare("UPDATE courses SET 
                                             name = :name, 
                                             periods = :periods
                                          WHERE id = :id;");
      $stmt->bindValue(':id', $course->getId(), \PDO::PARAM_INT);                                           
      $stmt->bindValue(':name', $course->getName(), \PDO::PARAM_STR);
      $stmt->bindValue(':periods', $course->getPeriods(), \PDO::PARAM_INT);
      $stmt->execute();
   }

   public function delete(int $id) {
      //executa a operação no banco
      $stmt = $this->connection->prepare("DELETE FROM courses 
                                          WHERE id = :id;");
      $stmt->bindValue(':id', $id, \PDO::PARAM_INT);                                           
      $stmt->execute();
   }
}