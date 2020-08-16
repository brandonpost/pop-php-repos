<?php

namespace App\Entity;

use App\Repository\GithubRepoRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GithubRepoRepository::class)
 */
class GithubRepo
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $repository_id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $url;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $created_date;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $last_push_date;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="integer")
     */
    private $stars;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getRepositoryId(): ?int
    {
        return $this->repository_id;
    }

    public function setRepositoryId(int $repository_id): self
    {
        $this->repository_id = $repository_id;

        return $this;
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

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getCreatedDate(): ?string
    {
        return $this->created_date;
    }

    public function setCreatedDate(string $created_date): self
    {
        $this->created_date = $created_date;

        return $this;
    }

    public function getLastPushDate(): ?string
    {
        return $this->last_push_date;
    }

    public function setLastPushDate(string $last_push_date): self
    {
        $this->last_push_date = $last_push_date;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getStars(): ?int
    {
        return $this->stars;
    }

    public function setStars(int $stars): self
    {
        $this->stars = $stars;

        return $this;
    }
}
