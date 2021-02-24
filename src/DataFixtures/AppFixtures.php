<?php

namespace App\DataFixtures;

use App\Entity\Project;
use App\Entity\SchoolYear;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;
    private $manager;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;
        $this->faker = \Faker\Factory::create('fr_FR');

        // @todo créer un faux utilisateur sans aucun privilège mais avec l'id 1

        // créer un user ROLE_ADMIN
        $user = new User();

        $firstname = 'Foo';
        $lastname = 'Foo';
        $email = 'foo.foo@example.com';
        $roles = ["ROLE_ADMIN"];
        $password = $this->encoder->encodePassword($user, '123');        
        $phone = null;
        
        $user->setFirstname($firstname)
            ->setLastname($lastname)
            ->setEmail($email)
            ->setPhone($phone)
            ->setRoles($roles)
            ->setPassword($password);

        $this->manager->persist($user);
        $this->manager->flush();

        // @todo ajouter un user de chaque rôle avec des propriétés déterminées à l'avance

        $this->loadUser(60, "ROLE_STUDENT");
        $this->loadUser(5, "ROLE_TEACHER");
        $this->loadUser(15, "ROLE_CLIENT");

        $this->loadProject(20);

        $this->loadSchoolYear(3);

        // 3 school years
        $this->loadUserSchoolYearRelation(3);

        // @todo ajouter les relations entre students et projects
        // @todo ajouter les relations entre clients et projects
    }

    public function loadUser(int $count, string $role): void
    {
        // students
        for ($i = 0; $i < $count; $i++) {
            $user = new User();

            $firstname = $this->faker->firstName();
            $lastname = $this->faker->lastName();
            $email = strtolower($firstname).'.'.strtolower($lastname).'-'.$i.'@example.com';
            $roles = [$role];
            $password = $this->encoder->encodePassword($user, '123');
            
            $phone = $this->faker->phoneNumber();
            
            $user->setFirstname($firstname)
                ->setLastname($lastname)
                ->setEmail($email)
                ->setPhone($phone)
                ->setRoles($roles)
                ->setPassword($password);

            $this->manager->persist($user);
        }

        $this->manager->flush();
    }

    public function loadProject(int $count): void
    {
        for ($i = 0; $i < $count; $i++) {
            $name = $this->faker->realText(100);
            // ajouter une description 25 fois sur 100 (c-à-d environ 1 fois sur 4)
            $description = null;

            if (random_int(1, 100) <= 25) {
                $description = $this->faker->realText(200);
            }
            
            $project = new Project();
            $project->setName($name)
                ->setDescription($description);

            $this->manager->persist($project);
        }

        $this->manager->flush();
    }

    public function loadSchoolYear(int $count): void
    {
        // il y a 2 school years par an
        // la première démarre le 01/01
        // la deuxième démarre le 01/07

        // la création de school years démarre en 2020
        $year = 2020;

        for ($i = 0; $i < $count; $i++) {
            $name = $this->faker->realText(100);
            $dateStart = new DateTime();
            $dateEnd = new DateTime();

            if ($i % 2 == 0) {
                // nombre pair
                // la school year démarre le 01/01
                $dateStart->setDate($year, 1, 1);
                $dateEnd->setDate($year, 6, 30);
            } else {
                // nombre impair
                // la school year démarre le 01/07
                $dateStart->setDate($year, 7, 1);
                $dateEnd->setDate($year, 12, 31);
            }

            // incrémentation de l'année toutes les 2 school years
            if ($i % 2 != 0) {
                $year++;
            }

            $schoolYear = new SchoolYear();
            $schoolYear->setName($name)
                ->setDateStart($dateStart)
                ->setDateEnd($dateEnd);
            
            $this->manager->persist($schoolYear);
        }

        $this->manager->flush();
    }

    public function loadUserSchoolYearRelation(int $countSchoolYear): void
    {
        $schoolYearRepository = $this->manager->getRepository(SchoolYear::class);
        $userRepository = $this->manager->getRepository(User::class);
        
        $schoolYears = $schoolYearRepository->findAll();

        // récupération de la liste des students avec la méthode array_filter()
        $users = $userRepository->findAll();
        $students = array_filter($users, function($user) {
            return in_array('ROLE_STUDENT', $user->getRoles());
        });

        // récupération de la liste des students avec une méthode personnalisée du repository
        // $students = $userRepository->findByRole('ROLE_STUDENT');

        foreach ($students as $i => $student) {
            $remainder = $i % $countSchoolYear;
            $student->setSchoolYear($schoolYears[$remainder]);

            $this->manager->persist($student);
        }

        $this->manager->flush();
    }
}
