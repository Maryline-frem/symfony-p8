<?php

namespace App\Controller;

use App\Repository\ProjectRepository;
use App\Repository\SchoolYearRepository;
use App\Repository\StudentRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    /**
     * @Route("/test", name="test")
     */
    public function index(
        ProjectRepository $projectRepository,
        SchoolYearRepository $SchoolYearRepository,
        StudentRepository $studentRepository,
        UserRepository $userRepository): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $admins = $userRepository->findAllAdmins();
        $lastAdmin = end($admins);

        dump($lastAdmin);

        if ($lastAdmin->getEmail() != 'admin@example.com') {
            $entityManager->remove($lastAdmin);
            $entityManager->flush();
        }

        $SchoolYears = $SchoolYearRepository->findAll();
        $SchoolYear = $SchoolYears[5];

        $projects = $projectRepository->findAll();
        $project1 = $projects[0];
        $project2 = $projects[1];
        $project3 = $projects[2];

        foreach ($project1->getStudents() as $studentProject1) {
            dump($studentProject1);
        }

        $student = $studentRepository->findAll()[10];
        dump($student);
        $student->setPhone('0687654321');
        $student->setSchoolYear($SchoolYear);
        $student->removeProject($project1);
        $student->addProject($project2);
        $student->addProject($project3);

        $entityManager->flush();
        dump($student);
        exit();

        $users = $userRepository->findAll();
        dump($users);

        $admins = $userRepository->findAllAdmins();
        dump($admins);

        $studentRoles = $userRepository->findByRole('ROLE_STUDENT');

        $user = $users[0];
        $student = $studentRepository->findOneByUser($user, 'ROLE_STUDENT');
        dump($user);
        dump($student);
        exit();

        $admin = $userRepository->find(1);
        dump($admin);

        $students = $studentRepository->findAll();
        dump($students);

        $firstStudent = $students[0];
        $user = $firstStudent->getUser();
        dump($user);

        $firstStudent = $studentRepository->find(1);
        dump($firstStudent);

        exit();
    }
}
