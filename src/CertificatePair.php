<?php

namespace SashaBo\SelfSignedCerts;

class CertificatePair
{
    public function __construct(
        private readonly \OpenSSLAsymmetricKey $privateKey,
        private readonly \OpenSSLCertificate $certificate
    ) {
    }

    public function getCertificate(): \OpenSSLCertificate
    {
        return $this->certificate;
    }

    public function getPrivateKey(): \OpenSSLAsymmetricKey
    {
        return $this->privateKey;
    }

    public function exportCertificate(): string
    {
        $certificate = '';
        openssl_x509_export($this->certificate, $certificate);
        return $certificate;
    }

    public function exportPrivateKey(?string $passPhrase = null): string
    {
        $privateKey = '';
        openssl_pkey_export($this->privateKey, $privateKey, $passPhrase);
        return $privateKey;
    }

    public function save(string $privateKeyPath, string $certPath, ?string $passPhrase = null): void
    {
        $this->mkdir($privateKeyPath);
        $this->mkdir($certPath);
        $privateKeyFile = fopen($privateKeyPath, 'w');
        if ($privateKeyFile === false) {
            throw new Exception('Can\'t open '.$privateKeyPath.' for writing private key');
        }
        $certFile = fopen($certPath, 'w');
        if ($certFile === false) {
            throw new Exception('Can\'t open '.$certPath.' for writing the certificate');
        }
        $privateKeyLines = fwrite($privateKeyFile, $this->exportPrivateKey($passPhrase));
        $certLines = fwrite($certFile, $this->exportCertificate());
        if ($privateKeyLines === false) {
            throw new Exception('Can\'t write to '.$privateKeyPath);
        }
        if ($certLines === false) {
            throw new Exception('Can\'t write to '.$certPath);
        }
    }

    private function mkdir($filename): void
    {
        $slashPos = strrpos($filename, '/');
        if ($slashPos !== false) {
            $dir = substr($filename, 0, $slashPos);
            if (!is_dir($dir)) {
                $made = mkdir($dir, 0777, true);
                if (!$made) {
                    throw new Exception('Can\'t create directory '.$dir);
                }
            }
        }
    }
}
