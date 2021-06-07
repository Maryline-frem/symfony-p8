<?php

namespace App\DataFixtures;

use App\Entity\Project;
use App\Entity\Student;
use App\Entity\User;
use App\Entity\SchoolYear;
use App\Entity\Teacher;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as FakerFactory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture implements FixtureGroupInterface
{
    private $encoder;
    private $faker;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
        $this->faker = FakerFactory::create('fr_FR');
    }

    public static function getGroups(): array
    {
        return ['test'];
    }


    public function load(ObjectManager $manager)
    {
        $schoolYearCount = 10;
        $studentsPerSchoolYear = 24;
        $studentsCount = $studentsPerSchoolYear * $schoolYearCount;
        $studentsPerProject = 3;

        if ($studentsCount % $studentsPerProject == 0) {
            // valeur plancher
            $projectsCount = (int) ($studentsCount / $studentsPerProject);
        } else {
            // valeur plafond
            $projectsCount = (int) ($studentsCount / $studentsPerProject) + 1;
        }

        $this->loadAdmins($manager, 3);
        $schoolYears = $this->loadSchoolYears($manager, $schoolYearCount);
        $students = $this->loadStudents($manager, $schoolYears, $studentsPerSchoolYear, $studentsCount);
        $projects = $this->loadProjects($manager, $students, $studentsPerProject, $projectsCount);
        $teachers = $this->loadTeachers($manager, $projects, 20);

        $manager->flush();
    }

    public function loadAdmins(ObjectManager $manager, int $count)
    {
        $user = new User();
        $user->setEmail('admin@example.com');
        // hashage du mot de passe
        $password = $this->encoder->encodePassword($user, '123');
        $user->setPassword($password);
        $user->setRoles(['ROLE_ADMIN']);

        $manager->persist($user);

        for ($i = 1; $i < $count; $i++) {
            $user = new User();
            $user->setEmail($this->faker->email());
            // hashage du mot de passe
            $password = $this->encoder->encodePassword($user, '123');
            $user->setPassword($password);
            $user->setRoles(['ROLE_ADMIN']);

            $manager->persist($user);
        }
    }

    public function loadSchoolYears(ObjectManager $manager, int $count)
    {
        $schoolYears = [];

        $schoolYear = new SchoolYear();
        $schoolYear->setName('Lorem ipsum');
        $schoolYear->setStartDate(\DateTime::createFromFormat('Y-m-d H:i:s', '2010-01-01 00:00:00'));
        // récupération de la date de début
        $startDate = $schoolYear->getStartDate();
        // création de la date de fin à  partir de la date de début
        $endDate = \DateTime::createFromFormat('Y-m-d H:i:s', $startDate->format('Y-m-d H:i:s'));
        // ajout d'un interval de 4 mois à la date de début
        $endDate->add(new \DateInterval('P4M'));
        $schoolYear->setEndDate($endDate);

        $manager->persist($schoolYear);
        $schoolYears[] = $schoolYear;

        for ($i = 1; $i < $count; $i++) {
            $schoolYear = new SchoolYear();
            $schoolYear->setName($this->faker->name());
            $schoolYear->setStartDate($this->faker->dateTimeThisDecade());
            // récupération de la date de début
            $startDate = $schoolYear->getStartDate();
            // création de la date de fin à  partir de la date de début
            $endDate = \DateTime::createFromFormat('Y-m-d H:i:s', $startDate->format('Y-m-d H:i:s'));
            // ajout d'un interval de 4 mois à la date de début
            $endDate->add(new \DateInterval('P4M'));
            $schoolYear->setEndDate($endDate);

            $manager->persist($schoolYear);
            $schoolYears[] = $schoolYear;
        }

        return $schoolYears;
    }

    public function loadStudents(ObjectManager $manager, array $schoolYears, int $studentsPerSchoolYear, int $count)
    {
        $students = [];
        $schoolYearIndex = 0;

        $schoolYear = $schoolYears[$schoolYearIndex];

        $user = new User();
        $user->setEmail('student@example.com');
        // hashage du mot de passe
        $password = $this->encoder->encodePassword($user, '123');
        $user->setPassword($password);
        $user->setRoles(['ROLE_STUDENT']);

        $manager->persist($user);

        $student = new Student();
        $student->setFirstname('Student');
        $student->setLastname('Student');
        $student->setPhone('0612345678');
        $student->setSchoolYear($schoolYear);
        $student->setUser($user);

        $manager->persist($student);
        $students[] = $student;

        for ($i = 1; $i < $count; $i++) {
            $schoolYear = $schoolYears[$schoolYearIndex];

            if ($i % $studentsPerSchoolYear == 0) {
                $schoolYearIndex++;
            }

            $user = new User();
            $user->setEmail($this->faker->email());
            // hashage du mot de passe
            $password = $this->encoder->encodePassword($user, '123');
            $user->setPassword($password);
            $user->setRoles(['ROLE_STUDENT']);

            $manager->persist($user);

            $student = new Student();
            $student->setFirstname($this->faker->firstname());
            $student->setLastname($this->faker->lastname());
            $student->setPhone($this->faker->phoneNumber());
            $student->setSchoolYear($schoolYear);
            $student->setUser($user);

            $manager->persist($student);
            $students[] = $student;
        }

        return $students;
    }

    public function loadProjects(ObjectManager $manager, array $students, int $studentsPerProject, int $count)
    {
        $studentIndex = 0;
        $projects = [];

        // création du premier projet avec des données en dur
        $project = new Project();
        $project->setName('Hackathon');

        while (true) {
            $student = $students[$studentIndex];
            $project->addStudent($student);
            
            if (($studentIndex + 1) % $studentsPerProject == 0) {
                $studentIndex++;
                break;
            }

            $studentIndex++;
        }

        $manager->persist($project);
        $projects[] = $project;

        // création des projets suivants avec des données aléatoires
        for ($i = 1; $i < $count; $i++) {
            $project = new Project();
            $project->setName($this->faker->sentence(2));

            while (true) {
                $student = $students[$studentIndex];
                $project->addStudent($student);
        
                if (($studentIndex + 1) % $studentsPerProject == 0) {
                    $studentIndex++;
                    break;
                }

                $studentIndex++;
            }
        
            $manager->persist($project);
            $projects[] = $project;
        }

        return $projects;
    }

    public function loadTeachers(ObjectManager $manager, array $projects, int $count)
    {
        $teachers = [];
        
        $user = new User();
        $user->setEmail('teacher@example.com');

        $password = $this->encoder->encodePassword($user, '123');
        $user->setPassword($password);
        $user->setRoles(['ROLE_TEACHER']);

        $manager->persist($user);

        $teacher = new Teacher();
        $teacher->setFirstname('Teacher');
        $teacher->setLastname('Teacher');
        $teacher->setPhone('0612345678');

        $teacher->setUser($user);

        $firstProject = array_shift($projects);

        $teacher->addProject($projects[0]);

        $manager->persist($teacher);

        $teachers[] = $teacher;

        for ($i = 1; $i < $count; $i++) {
            $user = new User();
            $user->setEmail($this->faker->email());
    
            $password = $this->encoder->encodePassword($user, '123');
            $user->setPassword($password);
            $user->setRoles(['ROLE_TEACHER']);
    
            $manager->persist($user);

            $teacher = new Teacher();
            $teacher->setFirstname($this->faker->firstname());
            $teacher->setLastname($this->faker->lastname());
            $teacher->setPhone($this->faker->phoneNumber());
    
            $teacher->setUser($user);
    
            $projectsCount = random_int(0,10);

            $randomProjects = $this->faker->randomElements($projects, $projectsCount);

            foreach ($randomProjects as $randomProject) {
                $teacher->addProject($randomProject);
            }
            $teacher->addProject($projects[0]);
    
            $manager->persist($teacher);
    
            $teachers[] = $teacher;
        }

        return $teachers;
    }
}
