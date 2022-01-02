<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\EditUpdateRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class EditUpdate
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(message="Veuillez renseigner votre prénom")
     *  @Assert\Length(max= 15, maxMessage="Désolé, votre prénom ne peut pas faire plus de 15 caractères")
     * @Assert\Regex(
     *     pattern="/[0-9\:\@\&\#\'\p{P}\{\(\)\}\-\|\]\À\Á\Â\Ã\Ä\Å\Ç\È\Ê\Ë\Ì\Í\Î\Ï\Ò\Ó\Ô\Õ\Ö\Ù\Ú\Û\Ü\Ý\á\â\ã\ä\å\ç\è\ê\ë\ì\í\î\ï\ð\ò\ó\ô\õ\ö\ù\ú\û\ü\ý\ÿ\`\^\ç\@\à\=\ù\%\¨\$\£\_\!\/\;\,\?\<\>\+\*\µ\²\\\~\s+$]/",
     *     match=false,
     *     message="Les caractères spéciaux les chriffres et les espaces ne sont pas acceptés"
     * )
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(message="Veuillez renseigner votre nom")
     * @Assert\Length(max= 15, maxMessage="Désolé, votre nom ne peut pas faire plus de 15 caractères")
     * @Assert\Regex(
     *     pattern="/[0-9\:\@\&\#\'\p{P}\{\(\)\}\-\|\]\À\Á\Â\Ã\Ä\Å\Ç\È\Ê\Ë\Ì\Í\Î\Ï\Ò\Ó\Ô\Õ\Ö\Ù\Ú\Û\Ü\Ý\á\â\ã\ä\å\ç\è\ê\ë\ì\í\î\ï\ð\ò\ó\ô\õ\ö\ù\ú\û\ü\ý\ÿ\`\^\ç\@\à\=\ù\%\¨\$\£\_\!\/\;\,\?\<\>\+\*\µ\²\\\~\s+$]/",
     *     match=false,
     *     message="Les caractères spéciaux les chriffres et les espaces ne sont pas acceptés"
     * )
     */
    private $lastName;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }
}
