<?php

namespace Service;

use Error\APIException;
use Model\Course;
use Repository\CourseRepository;
use Repository\StudentRepository;

class CourseService
{
   private CourseRepository $repository;

   function __construct() {
      $this->repository = new CourseRepository();
   }

   function getCourses(?string $name): array
   {
      if ($name) {  
         if ($name === "") throw new APIException("Invalid search parameter!", 400);
         return $this->repository->findByName($name);
      } else
         return $this->repository->findAll();
   }

   function getCourseById(int $id): Course
   {
      $course = $this->repository->findById($id);
      if (!$course)
         throw new APIException("Course not found!", 404);
      return $course;
   }

   function getCourseStudents(int $id): array {
      $course = $this->getCourseById($id);

      $studentRepository = new StudentRepository();
      return $studentRepository->findByCourseId($course->getId());
   }

   function createNewCourse(string $name, int $periods): Course {
      $course = new Course(
         name: trim($name), 
         periods: $periods
      );
      $this->validateCourse($course);
      return $this->repository->create($course);
   }
   function updateCourse(int $id, string $name, int $periods): Course
   {
      $course = $this->getCourseById($id);
      $course->setName($name);
      $course->setPeriods($periods);
      $this->validateCourse($course);

      $this->repository->update($course);
      return $course;
   }

   function deleteCourse(int $id): void {

      $students = $this->getCourseStudents($id);
      if (count($students) > 0) throw new APIException("This course has students!", 409);
      $this->repository->delete($id);
   }

   private function validateCourse(Course $course) {
      if (strlen($course->getName() < 5)) throw new APIException("Invalid course name!", 400);
      if ($course->getPeriods() <= 0) throw new APIException("Number of periods must be greater than zero!", 400);
   }
}