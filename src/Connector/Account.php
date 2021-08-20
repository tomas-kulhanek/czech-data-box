<?php declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Connector;

use TomasKulhanek\CzechDataBox\Exception\PkcsCertificateException;

class Account
{
    public const LOGIN_NAME_PASSWORD = 'password';
    public const LOGIN_SPIS_CERT = 'cert';
    public const LOGIN_CERT_LOGIN_NAME_PASSWORD = 'certPassword';
    public const LOGIN_HOSTED_SPIS = 'hosted';

    private ?string $loginName = null;

    private ?string $dataBoxId = null;

    private ?string $password = null;

    private string $loginType = self::LOGIN_NAME_PASSWORD;

    private bool $production = true;

    private ?string $publicKey = null;

    private ?string $privateKey = null;

    private ?string $privateKeyPassPhrase = null;

    public function getLoginName(): ?string
    {
        return $this->loginName;
    }

    public function setLoginName(string $loginName): Account
    {
        $this->loginName = $loginName;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): Account
    {
        $this->password = $password;
        return $this;
    }

    public function getLoginType(): string
    {
        return $this->loginType;
    }

    public function setLoginType(string $loginType): Account
    {
        $this->loginType = $loginType;
        return $this;
    }

    public function getPublicKey(): ?string
    {
        return $this->publicKey;
    }

    public function getPrivateKey(): ?string
    {
        return $this->privateKey;
    }

    public function setPublicKey(string $publicKey): Account
    {
        $this->publicKey = $publicKey;
        return $this;
    }

    public function setPrivateKey(string $privateKey): Account
    {
        $this->privateKey = $privateKey;
        return $this;
    }

    public function getPrivateKeyPassPhrase(): ?string
    {
        return $this->privateKeyPassPhrase;
    }

    public function setPrivateKeyPassPhrase(string $privateKeyPassPhrase): Account
    {
        $this->privateKeyPassPhrase = $privateKeyPassPhrase;
        return $this;
    }

    public function getDataBoxId(): ?string
    {
        return $this->dataBoxId;
    }

    public function setDataBoxId(?string $dataBoxId): Account
    {
        $this->dataBoxId = $dataBoxId;
        return $this;
    }

    public function setPkcs12Certificate(string $pkcsContent, string $passPhrase): Account
    {
        $cert_array = [];
        if (!openssl_pkcs12_read($pkcsContent, $cert_array, $passPhrase)) {
            throw new PkcsCertificateException('Invalid PKCS12');
        }

        $this->setPublicKey($cert_array['cert'])
            ->setPrivateKey($cert_array['pkey'])
            ->setPrivateKeyPassPhrase($passPhrase);

        return $this;
    }

    public function usingCertificate(): bool
    {
        return in_array($this->getLoginType(), [self::LOGIN_HOSTED_SPIS, self::LOGIN_SPIS_CERT, self::LOGIN_CERT_LOGIN_NAME_PASSWORD], true);
    }

    public function isProduction(): bool
    {
        return $this->production;
    }

    public function setProduction(bool $production): void
    {
        $this->production = $production;
    }

}
