<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;


class PasswordUpdate
{

    private $oldPassword;

    /**
     * @Assert\Regex(pattern="/^(?=.*[a-z])(?=.*[\:\@\&\#\'\{\(\)\}\-\|\]\`\^\ç\@\à\=\ù\%\¨\$\£\_\!\/\;\,\?\<\>\+\*\µ\²\\\~])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d\:\@\&\#\'\{\(\)\}\-\|\]\`\^\ç\@\à\=\ù\%\¨\$\£\_\!\/\;\,\?\<\>\+\*\µ\²\\\~]{8,}$/", message="Pour votre sécurité, votre nouveau mot de passe doit faire au moins 8 caractères sans espaces et contenir au moins une majuscule, une minuscule, un chiffre et caractère spécial.")
     */
    private $newPassword;

    /**
     * @Assert\EqualTo(propertyPath="newPassword", message="Vous n'avez pas correctement confirmé votre mot de passe")
     */
    private $confirmPassword;

    public function getOldPassword(): ?string
    {
        return $this->oldPassword;
    }

    public function setOldPassword(string $oldPassword): self
    {
        $this->oldPassword = $oldPassword;

        return $this;
    }

    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }

    public function setNewPassword(string $newPassword): self
    {
        $this->newPassword = $newPassword;

        return $this;
    }

    public function getConfirmPassword(): ?string
    {
        return $this->confirmPassword;
    }

    public function setConfirmPassword(string $confirmPassword): self
    {
        $this->confirmPassword = $confirmPassword;

        return $this;
    }
}
