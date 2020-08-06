<?php
/**
 * UnsignedAttributes
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Adapik/CMS
 */

namespace Adapik\CMS;

use Adapik\CMS\Exception\FormatException;
use FG\ASN1\ASN1ObjectInterface;
use FG\ASN1\Exception\Exception;
use FG\ASN1\Exception\ParserException;
use FG\ASN1\ExplicitlyTaggedObject;
use FG\ASN1\Universal\ObjectIdentifier;
use FG\ASN1\Universal\Sequence;
use FG\ASN1\Universal\Set;

/**
 * Class UnsignedAttributes
 *
 * @see     Maps\UnsignedAttributes
 * @package Adapik\CMS
 */
class UnsignedAttributes extends CMSBase
{
    /**
     * @var ExplicitlyTaggedObject
     */
    protected $object;

    /**
     * @param string $content
     *
     * @return UnsignedAttributes
     * @throws FormatException
     */
    public static function createFromContent(string $content)
    {
        return new self(self::makeFromContent($content, Maps\UnsignedAttributes::class, ExplicitlyTaggedObject::class));
    }

    /**
     * @param string $oid
     * @return ASN1ObjectInterface|Sequence|null
     * @throws ParserException
     */
    public function getByOid(string $oid)
    {
        return $this->getAttributeAsObject($oid);
    }

    /**
     * @param $oid
     * @return Sequence|ASN1ObjectInterface|null
     * @throws ParserException
     */
    private function getAttributeAsObject($oid)
    {
        $attribute = $this->findByOid($oid);

        if ($attribute) {
            $binary = $attribute->getBinary();
            return Sequence::fromBinary($binary);
        }

        return null;
    }

    /**
     * @param $oid
     * @return ASN1ObjectInterface|null
     */
    protected function findByOid($oid)
    {
        foreach ($this->object->getChildren() as $child) {
            if ($child->getChildren()[0]->__toString() == $oid) {
                return $child;
            }
        }

        return null;
    }

    /**
     * @return ASN1ObjectInterface|Sequence|null
     * @throws ParserException
     */
    public function getCertificateRefs()
    {
        return $this->getAttributeAsObject(CompleteCertificateRefs::getOid());
    }

    /**
     * @return ASN1ObjectInterface|Sequence|null
     * @throws ParserException
     */
    public function getRevocationRefs()
    {
        return $this->getAttributeAsObject(CompleteRevocationRefs::getOid());
    }

    /**
     * @return ASN1ObjectInterface|Sequence|null
     * @throws ParserException
     */
    public function getCertificateValues()
    {
        return $this->getAttributeAsObject(CertificateValues::getOid());
    }

    /**
     * @return ASN1ObjectInterface|Sequence|null
     * @throws ParserException
     */
    public function getEscTimeStamp()
    {
        return $this->getAttributeAsObject(EscTimeStamp::getOid());
    }

    /**
     * @return RevocationValues|null|CMSInterface
     */
    public function getRevocationValues()
    {
        return $this->getAttributeAsInstance(RevocationValues::class);
    }

    /**
     * @param string $class
     * @return CMSInterface|null
     */
    private function getAttributeAsInstance(string $class)
    {
        $attribute = $this->findByOid(call_user_func($class . '::getOid'));

        if ($attribute) {
            return new $class($attribute);
        }

        return null;
    }

	/**
	 * Sometimes having Cryptographic Message Syntax (CMS) we need to store OCSP check response for the
	 * signer certificate, otherwise CMS data means nothing.
	 *
	 * @param BasicOCSPResponse $basicOCSPResponse
	 *
	 * @return UnsignedAttributes
	 * @throws Exception
	 * @throws ParserException
	 * @todo move to extended package
	 */
    public function setRevocationValues(BasicOCSPResponse $basicOCSPResponse)
    {
        $binary = $basicOCSPResponse->getBinary();

		$revocationValues = Sequence::create([
			ObjectIdentifier::create(RevocationValues::getOid()),
			Set::create([
				Sequence::create([
					ExplicitlyTaggedObject::create(1,
						Sequence::create([
							Sequence::fromBinary($binary),
						]
						)
					),
				]
				),
			]
			),
		]
		);

        $current = $this->findByOid(RevocationValues::getOid());

        if ($current) {
            $this->object->replaceChild($current, $revocationValues);
        } else {
            $this->object->appendChild($revocationValues);
        }

        return $this;
    }

    /**
     * @return TimeStampToken|CMSInterface|null
     */
    public function getTimeStampToken()
    {
        return $this->getAttributeAsInstance(TimeStampToken::class);
    }
}