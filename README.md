# self-signed-certs

PHP library for creating self-signed certificates. No dependencies.

### Installation

`composer require sashabo/self-signed-certs`

## Usage

```php
<?php

use SashaBo\SelfSignedCerts\CertificateGenerator;

$generator = new CertificateGenerator();
$generator
    ->setName('Some Name')
    ->setEmail('somebody@example.com')
    ->setAddress('CC', 'State or province', 'City')
    ->setOrganization('Organization', 'Department')
;

$pair = $generator->generate();
$pair->save('/etc/nginx/ssl/my.key', '/etc/nginx/ssl/my.crt');
```

### CertificateGenerator methods:

`generate(): CertificatePair`

These settings are not necessary, you may use defaults in most cases:

`setExpireDays(int $days): static` - 3650 by default

`setPrivateKeyBits(int $bits): static` - 4096 by default

`setPrivateKeyType(int $type): static` - OPENSSL_KEYTYPE_RSA by default

`setDigestAlgorithm(string $algorithm): static` - AES-128-CBC by default

`setOpenSslConfPath(string $path): static` - /etc/ssl/openssl.cnf by default 
(standard path for Ubuntu and most of linuxes)

These settings override openssl.cnf settings:

`setName(string $name): static`

`setEmail(string $email): static`

`setAddress(string $countryCode, string $stateOrProvince, string $city): static`

`setOrganization(string $name, string $unit): static`

### CertificatePair methods:

`getPrivateKey(): \OpenSSLAsymmetricKey`

`getCertificate(): \OpenSSLCertificate`

`exportPrivateKey(?string $passPhrase = null): string`

`exportCertificate(): string`

`save(string $privateKeyPath, string $certPath, ?string $passPhrase = null): void`

