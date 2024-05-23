<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO\Request;

use Exception;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

use function mb_substr;

#[Serializer\XmlNamespace(uri: 'https://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\XmlRoot(namespace: 'https://isds.czechpoint.cz/v20', name: 'ChangeISDSPassword')]
class ChangeISDSPassword implements IRequest
{
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('dbOldPassword')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Assert\NotBlank(allowNull: false)]
    protected string $oldPassword;

    #[Serializer\Type('string')]
    #[Serializer\SerializedName('dbNewPassword')]
    #[Serializer\XmlElement(cdata: false, namespace: 'https://isds.czechpoint.cz/v20')]
    #[Assert\NotBlank(allowNull: false)]
    protected string $newPassword;

    public function getOldPassword(): string
    {
        return $this->oldPassword;
    }

    public function setOldPassword(string $oldPassword): ChangeISDSPassword
    {
        $this->oldPassword = $oldPassword;
        return $this;
    }

    public function getNewPassword(): string
    {
        return $this->newPassword;
    }

    public function setNewPassword(string $newPassword): ChangeISDSPassword
    {
        $pwdLen = mb_strlen($newPassword);
        if (
            $pwdLen < 8 ||
            $pwdLen > 32 ||
            preg_match('/^[a-z0-9\!\#$\*\%\&\(\)\*\+\,\-\:\=\?\@\[\]\_\{\|\}\~]+$/i', $newPassword) === false ||
            preg_match('/[A-Z]/', $newPassword) === false ||
            preg_match('/[a-z]/', $newPassword) === false ||
            preg_match('/[0-9]/', $newPassword) === false ||
            in_array(mb_substr($newPassword, 0, 5), ['qwert', 'asdgf']) ||
            mb_substr($newPassword, 0, 6) === '123456'
        ) {
            throw new Exception('Password does not meet the requirements. Password must be between 8 and 32 chars and may not start with the following values "qwert", "asdgf", "123456". ');
        }

        $this->newPassword = $newPassword;
        return $this;
    }
}
