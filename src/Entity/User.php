<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 * @UniqueEntity(fields={"email"}, message="Un utilisateur s'est déjà inscrit avec cette adresse email, merci de le modifier")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Veuillez renseigner votre prénom")
     * @Assert\Length(max= 15, maxMessage="Désolé, votre prénom ne peut pas faire plus de 15 caractères")
     * @Assert\Regex(
     *     pattern="/[0-9\:\@\&\#\'\p{P}\{\(\)\}\-\|\]\À\Á\Â\Ã\Ä\Å\Ç\È\Ê\Ë\Ì\Í\Î\Ï\Ò\Ó\Ô\Õ\Ö\Ù\Ú\Û\Ü\Ý\á\â\ã\ä\å\ç\è\ê\ë\ì\í\î\ï\ð\ò\ó\ô\õ\ö\ù\ú\û\ü\ý\ÿ\`\^\ç\@\à\=\ù\%\¨\$\£\_\!\/\;\,\?\<\>\+\*\µ\²\\\~\s+$]/",
     *     match=false,
     *     message="Les caractères spéciaux les chriffres et les espaces ne sont pas acceptés"
     * )
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Veuillez renseigner votre nom")
     * @Assert\Length(max= 15, maxMessage="Désolé, votre prénom ne peut pas faire plus de 15 caractères")
     * @Assert\Regex(
     *     pattern="/[0-9\:\@\&\#\'\p{P}\{\(\)\}\-\|\]\À\Á\Â\Ã\Ä\Å\Ç\È\Ê\Ë\Ì\Í\Î\Ï\Ò\Ó\Ô\Õ\Ö\Ù\Ú\Û\Ü\Ý\á\â\ã\ä\å\ç\è\ê\ë\ì\í\î\ï\ð\ò\ó\ô\õ\ö\ù\ú\û\ü\ý\ÿ\`\^\ç\@\à\=\ù\%\¨\$\£\_\!\/\;\,\?\<\>\+\*\µ\²\\\~\s+$]/",
     *     match=false,
     *     message="Les caractères spéciaux les chriffres et les espaces ne sont pas acceptés"
     * )
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Email(message="Veuillez renseigner un email valide")
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * * @Assert\File(
    * maxSize = "4000k",
    * maxSizeMessage = "Le fichier ne peut dépasser 4mo",
    * mimeTypes = {"application/png", "application/x-png", "application/jpeg", "application/x-jpeg"},
    * mimeTypesMessage = "Seul les formats : jpeg, et png sont autorisés.")
     */
    private $picture;

    /**
     * @ORM\Column(type="string", length=255)
    * @Assert\Regex(pattern="/^(?=.*[a-z])(?=.*[\:\@\&\#\'\{\(\)\}\-\|\]\`\^\ç\@\à\=\ù\%\¨\$\£\_\!\/\;\,\?\<\>\+\*\µ\²\\\~])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d\:\@\&\#\'\{\(\)\}\-\|\]\`\^\ç\@\à\=\ù\%\¨\$\£\_\!\/\;\,\?\<\>\+\*\µ\²\\\~]{8,}$/", message="Pour votre sécurité, votre mot de passe doit faire au moins 8 caractères sans espaces et contenir au moins une majuscule, une minuscule, un chiffre et caractère spécial.")
     * 
     */
    private $hash;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $registrationToken;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isVerified;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $forgotPasswordToken;

    /**
     * @ORM\ManyToMany(targetEntity=Role::class, mappedBy="users")
     */
    private $roles;

    /**
     * @ORM\OneToOne(targetEntity=Sujet::class, mappedBy="author", cascade={"persist", "remove"})
     */
    private $sujet;

    /**
     * @ORM\OneToMany(targetEntity=Sujet::class, mappedBy="Authors")
     */
    private $sujets;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="author")
     */
    private $comments;

    /**
     * @Assert\EqualTo(propertyPath="hash", message="Vous n'avez pas correctement confirmé votre mot de passe")
     */
    private $confirm;

    public function __construct()
    {
        $this->roles = new ArrayCollection();
        $this->sujets = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function setHash(string $hash): self
    {
        $this->hash = $hash;

        return $this;
    }
    public function getSalt(){}
    public function getUsername(){
        return $this->email;
    }
    public function eraseCredentials(){}
    public function getPassword(){
        return $this->hash;
    }
    public function getRoles()
        {
            $roles = $this->roles->map(function($role){
                return $role->getTitle();
            })->toArray();
    
            // L'admin connecté aura aussi le role user en plus de celui d'amin
    
            $roles[] = 'ROLE_USER';
    
            // On retourne le role de la personne connectée
    
            return $roles;
         }

    public function getRegistrationToken(): ?string
    {
        return $this->registrationToken;
    }

    public function setRegistrationToken(?string $registrationToken): self
    {
        $this->registrationToken = $registrationToken;

        return $this;
    }

    public function getIsVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(?bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getForgotPasswordToken(): ?string
    {
        return $this->forgotPasswordToken;
    }

    public function setForgotPasswordToken(?string $forgotPasswordToken): self
    {
        $this->forgotPasswordToken = $forgotPasswordToken;

        return $this;
    }

    public function addRole(Role $role): self
    {
        if (!$this->roles->contains($role)) {
            $this->roles[] = $role;
            $role->addUser($this);
        }

        return $this;
    }

    public function removeRole(Role $role): self
    {
        if ($this->roles->removeElement($role)) {
            $role->removeUser($this);
        }

        return $this;
    }

    public function getSujet(): ?Sujet
    {
        return $this->sujet;
    }

    public function setSujet(?Sujet $sujet): self
    {
        // unset the owning side of the relation if necessary
        if ($sujet === null && $this->sujet !== null) {
            $this->sujet->setAuthor(null);
        }

        // set the owning side of the relation if necessary
        if ($sujet !== null && $sujet->getAuthor() !== $this) {
            $sujet->setAuthor($this);
        }

        $this->sujet = $sujet;

        return $this;
    }

    /**
     * @return Collection|Sujet[]
     */
    public function getSujets(): Collection
    {
        return $this->sujets;
    }

    public function addSujet(Sujet $sujet): self
    {
        if (!$this->sujets->contains($sujet)) {
            $this->sujets[] = $sujet;
            $sujet->setAuthors($this);
        }

        return $this;
    }

    public function removeSujet(Sujet $sujet): self
    {
        if ($this->sujets->removeElement($sujet)) {
            // set the owning side to null (unless already changed)
            if ($sujet->getAuthors() === $this) {
                $sujet->setAuthors(null);
            }
        }

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
            $comment->setAuthor($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getAuthor() === $this) {
                $comment->setAuthor(null);
            }
        }

        return $this;
    }

    public function getConfirm(): ?string
    {
        return $this->confirm;
    }

    public function setConfirm(?string $confirm): self
    {
        $this->confirm = $confirm;

        return $this;
    }

   
}
