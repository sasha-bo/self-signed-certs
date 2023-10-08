<?php

namespace SashaBo\SelfSignedCerts;

use JetBrains\PhpStorm\NoReturn;

class CertificateGenerator
{
    protected int $expireDays = 3650;
    protected int $privateKeyBits = 4096;
    protected int $privateKeyType = OPENSSL_KEYTYPE_RSA;
    protected string $digestAlg = 'AES-128-CBC';
    protected string $openSslConfPath = '/etc/ssl/openssl.cnf';
    protected ?string $commonName = null;
    protected ?string $emailAddress = null;
    protected ?string $countryName = null;
    protected ?string $stateOrProvinceName = null;
    protected ?string $localityName = null;
    protected ?string $organizationName = null;
    protected ?string $organizationalUnitName = null;

    #[NoReturn]
    public function generate(): CertificatePair
    {
        // configs
        $config = $this->getConfig();
        $distinguishedNames = $this->getDistinguishedNames();
        // generating private key
        $privateKey = openssl_pkey_new($config);
        if ($privateKey === false) {
            throw new Exception('openssl_pkey_new returned false');
        }
        // generating sign request
        $request = openssl_csr_new($distinguishedNames, $privateKey, $config);
        if ($request === false) {
            throw new Exception('openssl_csr_new returned false');
        }
        // signing
        $certificate = openssl_csr_sign($request, null, $privateKey, $this->expireDays, $config, 0 );
        if ($certificate === false) {
            throw new Exception('openssl_csr_sign returned false');
        }
        return new CertificatePair($privateKey, $certificate);
    }

    protected function getConfig(): array
    {
        return [
            'config'            =>  $this->openSslConfPath,
            'digest_alg'        =>  $this->digestAlg,
            'private_key_bits'  =>  $this->privateKeyBits,
            'private_key_type'  =>  $this->privateKeyType,
            'encrypt_key'       =>  false
        ];
    }

    protected function getDistinguishedNames(): array
    {
        $names = [
            "countryName"               => $this->countryName,
            "stateOrProvinceName"       => $this->stateOrProvinceName,
            "localityName"              => $this->localityName,
            "organizationName"          => $this->organizationName,
            "organizationalUnitName"    => $this->organizationalUnitName,
            "commonName"                => $this->commonName,
            "emailAddress"              => $this->emailAddress
        ];
        foreach ($names as $key => $value) {
            if (is_null($value)) {
                unset($names[$key]);
            }
        }

        return $names;
    }

    public function setExpireDays(int $days): static
    {
        $this->expireDays = $days;

        return $this;
    }

    public function setPrivateKeyBits(int $bits): static
    {
        $this->privateKeyBits = $bits;

        return $this;
    }

    public function setPrivateKeyType(int $type): static
    {
        $this->privateKeyType = $type;

        return $this;
    }

    public function setDigestAlgorithm(string $algorithm): static
    {
        $this->digestAlg = $algorithm;

        return $this;
    }

    public function setOpenSslConfPath(string $path): static
    {
        $this->openSslConfPath = $path;

        return $this;
    }

    public function setName(string $name): static
    {
        $this->commonName = $name;

        return $this;
    }

    public function setEmail(string $email): static
    {
        $this->emailAddress = $email;

        return $this;
    }

    public function setAddress(string $countryCode, string $stateOrProvince, string $city): static
    {
        $this->countryName = $countryCode;
        $this->stateOrProvinceName = $stateOrProvince;
        $this->localityName = $city;

        return $this;
    }

    public function setOrganization(string $name, string $unit): static
    {
        $this->organizationName = $name;
        $this->organizationalUnitName = $unit;

        return $this;
    }
}