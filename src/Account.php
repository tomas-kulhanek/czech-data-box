<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox;

use SensitiveParameter;
use TomasKulhanek\CzechDataBox\Enum\LoginTypeEnum;
use TomasKulhanek\CzechDataBox\Exception\PkcsCertificateException;

class Account
{
    /**
     * Placeholder shown instead of credentials when the account is dumped.
     */
    private const string REDACTED = '***';

    private ?string $loginName = null;
    private ?string $dataBoxId = null;
    private ?string $password = null;
    private LoginTypeEnum $loginType = LoginTypeEnum::NAME_PASSWORD;
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

    public function setPassword(#[SensitiveParameter] string $password): Account
    {
        $this->password = $password;
        return $this;
    }

    public function getLoginType(): LoginTypeEnum
    {
        return $this->loginType;
    }

    public function setLoginType(LoginTypeEnum $loginType): Account
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

    public function setPrivateKey(#[SensitiveParameter] string $privateKey): Account
    {
        $this->privateKey = $privateKey;
        return $this;
    }

    public function getPrivateKeyPassPhrase(): ?string
    {
        return $this->privateKeyPassPhrase;
    }

    public function setPrivateKeyPassPhrase(#[SensitiveParameter] string $privateKeyPassPhrase): Account
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

    public function setPkcs12Certificate(
        #[SensitiveParameter] string $pkcsContent,
        #[SensitiveParameter] string $passPhrase
    ): Account {
        $cert_array = [];
        if (!openssl_pkcs12_read($pkcsContent, $cert_array, $passPhrase)) {
            throw new PkcsCertificateException('Invalid PKCS12');
        }
        if (!is_array($cert_array)) {
            throw new PkcsCertificateException('Invalid PKCS12');
        }
        $cert = $cert_array['cert'] ?? null;
        $pkey = $cert_array['pkey'] ?? null;
        if (!is_string($cert) || !is_string($pkey)) {
            throw new PkcsCertificateException('PKCS12 does not contain a certificate and a private key');
        }

        $this->setPublicKey($cert)
            ->setPrivateKey($pkey)
            ->setPrivateKeyPassPhrase($passPhrase);

        return $this;
    }

    /**
     * Keeps credentials out of var_dump()/print_r() output and of anything built on top of them.
     *
     * @return array<string, mixed>
     */
    public function __debugInfo(): array
    {
        return [
            'loginName' => $this->loginName,
            'dataBoxId' => $this->dataBoxId,
            'password' => $this->password === null ? null : self::REDACTED,
            'loginType' => $this->loginType,
            'publicKey' => $this->publicKey,
            'privateKey' => $this->privateKey === null ? null : self::REDACTED,
            'privateKeyPassPhrase' => $this->privateKeyPassPhrase === null ? null : self::REDACTED,
        ];
    }

    public function usingCertificate(): bool
    {
        return match ($this->getLoginType()) {
            LoginTypeEnum::HOSTED_SPIS, LoginTypeEnum::SPIS_CERT, LoginTypeEnum::CERT_LOGIN_NAME_PASSWORD => true,
            default => false,
        };
    }
}
