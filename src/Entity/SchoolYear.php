<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\SchoolYearRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource
 * @ORM\Entity(repositoryClass=SchoolYearRepository::class)
 * @UniqueEntity("name")
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(columns={"name"})})
 */
class SchoolYear
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank
     * @Assert\Length(
     *      min = 3,
     *      max = 190
     * )
     * @ORM\Column(type="string", length=190)
     */
    private $name;

    /**
     * @Assert\Type("\DateTimeInterface")
     * Assert\GreaterThan("-10 years")
     * @Assert\GreaterThan(
     *  value = "2010-01-01"
     * )
     * @ORM\Column(type="date", nullable=true)
     */
    private $dateStart;

    /**
     * @Assert\Type("\DateTimeInterface")
     * @Assert\GreaterThan("today")
     * @ORM\Column(type="date", nullable=true)
     */
    private $dateEnd;

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="schoolYear")
     */
    private $users;

    public function __construct()
    {
        $this->dateStart = new \DateTime();
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDateStart(): ?\DateTimeInterface
    {
        return $this->dateStart;
    }

    public function setDateStart(?\DateTimeInterface $dateStart): self
    {
        $this->dateStart = $dateStart;

        return $this;
    }

    public function getDateEnd(): ?\DateTimeInterface
    {
        return $this->dateEnd;
    }

    public function setDateEnd(?\DateTimeInterface $dateEnd): self
    {
        $this->dateEnd = $dateEnd;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setSchoolYear($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getSchoolYear() === $this) {
                $user->setSchoolYear(null);
            }
        }

        return $this;
    }
}
