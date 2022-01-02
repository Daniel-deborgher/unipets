<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\SujetRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=SujetRepository::class)
 * @UniqueEntity(fields={"titre"}, message="Un sujet a déjà été créé avec ce titre, nous vous invitons à modifier le titre ou effectuer une recherche pour voir les réponses.")
 */
class Sujet
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $titre;

    /**
     * @ORM\Column(type="text")
     */
    private $contenu;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $image;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="sujets")
     */
    private $category;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $slug;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\OneToOne(targetEntity=User::class, inversedBy="sujet", cascade={"persist", "remove"})
     */
    private $author;

    

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="sujets")
     */
    private $Authors;

    /**
     * @ORM\JoinColumn(name="comments_id", referencedColumnName="id", onDelete="CASCADE")
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="sujet", cascade={"remove"})
     */
    private $comments;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $averti;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $desable;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): self
    {
        $this->contenu = $contenu;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getAutheur(): ?string
    {
        return $this->autheur;
    }

    public function setAutheur(?string $autheur): self
    {
        $this->autheur = $autheur;

        return $this;
    }

    public function getAuteur(): ?string
    {
        return $this->auteur;
    }

    public function setAuteur(?string $auteur): self
    {
        $this->auteur = $auteur;

        return $this;
    }

    public function getAuthors(): ?User
    {
        return $this->Authors;
    }

    public function setAuthors(?User $Authors): self
    {
        $this->Authors = $Authors;

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setSujet($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getSujet() === $this) {
                $comment->setSujet(null);
            }
        }

        return $this;
    }

    public function getAverti(): ?bool
    {
        return $this->averti;
    }

    public function setAverti(?bool $averti): self
    {
        $this->averti = $averti;

        return $this;
    }

    public function getDesable(): ?string
    {
        return $this->desable;
    }

    public function setDesable(?string $desable): self
    {
        $this->desable = $desable;

        return $this;
    }
}
