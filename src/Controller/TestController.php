<?php

namespace App\Controller;

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
    public function index(StudentRepository $studentRepository, UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();
        dump($users);

        $user = $users[50];
        $student = $studentRepository->findOneByUser($user);
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
