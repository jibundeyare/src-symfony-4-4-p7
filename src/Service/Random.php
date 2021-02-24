<?php

namespace App\Service;

use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;

class Random
{
    private $em;
    private $repository;
    private $meteoUrl;

    public function __construct(EntityManagerInterface $em, ProjectRepository $repository, string $meteoUrl)
    {
        $this->em = $em;
        $this->repository = $repository;
        $this->meteoUrl = $meteoUrl;
    }

    public function getMeteoUrl()
    {
        return $this->meteoUrl;
    }

    public function getProjects()
    {
        return $this->repository->findAll();
    }

    public function getInt(int $min = 0, int $max = 100): int
    {
        return random_int($min, $max);
    }
}
