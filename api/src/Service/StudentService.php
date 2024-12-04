<?php

namespace Service;

use Error\APIException;
use Model\Student;
use Repository\CourseRepository;
use Repository\StudentRepository;

class StudentService {
   private StudentRepository $repository;

   function __construct() {
      //cria o repositório de estudantes
      $this->repository = new StudentRepository();
   }

   function getStudents(?string $name): array {
      //se recebeu um parâmetro de busca, filtra!
      if ($name) return $this->repository->findByName($name);
      else return $this->repository->findAll();
   }

   function getStudentById(string $id): Student {
      //busca o estudante pelo Id
      $student = $this->repository->findById($id);

      //se não encontrar, gera uma exceção
      if (!$student) throw new APIException("Student not found!", 404);

      //retorna o estudante encontrado
      return $student;
   }

   function createNewStudent(
      string $name,
      string $email,
      int $courseId,
      int $period
   ): Student {

      //cria um novo estudantes com os dados recebidos
      $student = new Student(
         name: $name,
         email: $email,
         courseId: $courseId,
         period: $period
      );

      //verifica as regras de negócio
      $this->validadeStudent($student);

      //salva o estudante no banco de dado
      return $this->repository->create($student);
   }
   function updateStudent(
      string $id,
      string $name,
      string $email,
      int $courseId,
      int $period
   ): Student {

      //busca o estudante pelo id
      $student = $this->getStudentById($id);
      
      //atualiza os valores das propriedades
      $student->setName($name);
      $student->setEmail($email);
      $student->setCourseId($courseId);
      $student->setPeriod($period);

      //verifica as regras de negócio
      $this->validadeStudent($student);

      //salva as alterações no banco
      $this->repository->update($student);

      //retorna o estudante atualizado
      return $student;
   }

   function deleteStudent(string $id) {
      //busca o estudante pelo Id para verificar se existe
      $student = $this->getStudentById($id);

      //Exclui o estudante no banco de dados
      $this->repository->delete($id);
   }

   function setStudentPeriod(string $id, int $period): Student {
      //busca o estudante pelo Id
      $student = $this->getStudentById($id);
      
      //atualiza o período do estudante
      $student->setPeriod($period);

      //valida se o estudante atualizado está de acordo com as regras
      $this->validadeStudent($student);
 
      //altera o período do estudante
      $this->repository->setPeriod($id, $period);

      //retorna o estudante atualizado
      return $student;
   }

   private function validadeStudent(Student $student) {
      //verifica se o nome do estudante tem pelo menos 5 caracters
      if (strlen($student->getName()) < 5) throw new APIException("Invalid student name!", 400);
     
      //verificar se o email é válido
      if (!filter_var($student->getEmail(), FILTER_VALIDATE_EMAIL)) throw new APIException("Invalid email!", 400);

      //verifica se exites um estudante com o mesmo email
      $studentWithSameEmail = $this->repository->findByEmail($student->getEmail());
      if ($studentWithSameEmail) {
         //como pode ser um update, verificar se o email encontrado não é do próprio estudante
         if ($studentWithSameEmail->getId() !== $student->getId()) throw new APIException("This email is already in use!", 409);
      }

      //verifica se o Id do curso refere-se a um curso existente
      $courseRepototy = new CourseRepository();
      $course = $courseRepototy->findById($student->getCourseId());
      if (!$course) throw new APIException("Course not found!", 400);

      //verifica se o período do estudante é maior ou igual a zero
      if ($student->getPeriod() <= 0) throw new APIException("Period must be greater than zero!", 400);

      //verifica se o período do estudante não é maior do que o número de períodos do curso
      if ($student->getPeriod() > $course->getPeriods()) throw new APIException("Period is greater than course periods!", 400);
   }
}