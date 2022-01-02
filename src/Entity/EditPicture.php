<?php

namespace App\Entity;

use App\Repository\EditPictureRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;


class EditPicture
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\File(
    * maxSize = "4000k",
    * maxSizeMessage = "Le fichier ne peut dépasser 4mo",
    * mimeTypes = {"application/png", "application/x-png", "application/jpeg", "application/x-jpeg"},
    * mimeTypesMessage = "Seul les formats : jpeg, et png sont autorisés.")
     */
    private $picture;

    public function getId(): ?int
    {
        return $this->id;
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
}
