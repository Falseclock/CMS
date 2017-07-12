<?php

namespace Adapik\Test\CMS;

use Adapik\CMS\Certificate;
use PHPUnit\Framework\TestCase;

/**
 * Test Certificate class
 */
class CertificateTest extends TestCase
{
    const CERT_SERIAL         = '191475573341482871230183340876003493987';
    const CERT_SUBJECT_KEY_ID = '005ecbf504b0d74b3517cc4ebc1dc73e3731d237';
    const CERT_ISSUER_KEY_ID  = 'b390a7d8c9af4ecd613c9f7cad5d7f41fd6930ea';
    const CERT_OCSP_URI       = 'http://ocsp.usertrust.com';

    /**
     * Binary content of User Certificate file
     *
     * @var string
     */
    private $userCert;

    /**
     * Binary content of CA Certificate file
     *
     * @var string
     */
    private $caCert;

    public function setUp()
    {
        $this->userCert = base64_decode(file_get_contents(__DIR__ . '/../fixtures/phpnet.crt'));
        $this->caCert   = base64_decode(file_get_contents(__DIR__ . '/../fixtures/cacert.crt'));
    }

    public function testParseCert()
    {
        $cert = Certificate::createFromContent($this->userCert);
        $this->assertInstanceOf(Certificate::class, $cert);
    }

    public function testGetSerial()
    {
        $cert = Certificate::createFromContent($this->userCert);
        $this->assertEquals(self::CERT_SERIAL, $cert->getSerial());
    }

    public function testGetSubjectKeyIdentifier()
    {
        $cert = Certificate::createFromContent($this->userCert);
        $this->assertEquals(self::CERT_SUBJECT_KEY_ID, $cert->getSubjectKeyIdentifier());
    }

    public function testGetAuthorityKeyIdentifier()
    {
        $cert = Certificate::createFromContent($this->userCert);
        $this->assertEquals(self::CERT_ISSUER_KEY_ID, $cert->getAuthorityKeyIdentifier());
    }

    public function testGetOcspUris()
    {
        $cert = Certificate::createFromContent($this->userCert);
        $this->assertEquals([self::CERT_OCSP_URI], $cert->getOcspUris());
    }

    public function testGetIssuer()
    {
        $cert = Certificate::createFromContent($this->userCert);
        $this->assertEquals('Country Name: FR; State or Province Name: Paris; Locality Name: Paris; Organization Name: Gandi; Common Name: Gandi Standard SSL CA 2', (string) $cert->getIssuer());
    }

    public function testGetSubject()
    {
        $cert = Certificate::createFromContent($this->userCert);
        $this->assertEquals('Organization Unit Name: Domain Control Validated; Organization Unit Name: Gandi Standard Wildcard SSL; Common Name: *.php.net', (string) $cert->getSubject());
    }

    public function testGetValidNotBefore()
    {
        $cert = Certificate::createFromContent($this->userCert);
        $this->assertEquals('2016-06-02T00:00:00+00:00', (string) $cert->getValidNotBefore());
    }

    public function testGetValidNotAfter()
    {
        $cert = Certificate::createFromContent($this->userCert);
        $this->assertEquals('2019-06-02T23:59:59+00:00', (string) $cert->getValidNotAfter());
    }

    public function testIsCAFalse()
    {
        $cert = Certificate::createFromContent($this->userCert);
        $this->assertFalse($cert->isCa());
    }

    public function testIsCATrue()
    {
        $cert = Certificate::createFromContent($this->caCert);
        $this->assertTrue($cert->isCa());
    }
}
