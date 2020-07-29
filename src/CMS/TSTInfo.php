<?php
/**
 * TSTInfo
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Adapik/CMS
 */

namespace Adapik\CMS;

use Adapik\CMS\Exception\FormatException;
use Exception;
use FG\ASN1\ASN1ObjectInterface;
use FG\ASN1\ExplicitlyTaggedObject;
use FG\ASN1\Universal\Boolean;
use FG\ASN1\Universal\GeneralizedTime;
use FG\ASN1\Universal\Integer;
use FG\ASN1\Universal\ObjectIdentifier;
use FG\ASN1\Universal\Sequence;

/**
 * Class TSTInfo
 *
 * @see     Maps\TSTInfo
 * @package Adapik\CMS
 */
class TSTInfo extends CMSBase
{
    /**
     * @var Sequence
     */
    protected $object;

    /**
     * @param string $content
     *
     * @return TSTInfo
     * @throws FormatException
     */
    public static function createFromContent(string $content)
    {
        return new self(self::makeFromContent($content, Maps\TSTInfo::class, Sequence::class));
    }

    /**
     * @return Accuracy|null
     * @throws Exception
     */
    public function getAccuracy()
    {
        /** @var Sequence[] $sequences */
        $sequences = $this->object->findChildrenByType(Sequence::class);
        if (count($sequences) > 1) {
            return new Accuracy($sequences[1]);
        }

        return null;
    }

    /**
     * TODO: implement
     *
     * @return ExplicitlyTaggedObject|null
     * @throws Exception
     */
    /*	public function getExtensions() {
            $explicits = $this->object->findChildrenByType(ExplicitlyTaggedObject::class);
            foreach($explicits as $explicit) {
                if($explicit->getIdentifier()->getTagNumber() == 1) {
                    return $explicit;
                }
            }

            return null;
        }*/

    /**
     * @return GeneralizedTime|ASN1ObjectInterface
     * @throws Exception
     */
    public function getGenTime(): GeneralizedTime
    {
        $binary = $this->object->findChildrenByType(GeneralizedTime::class)[0]->getBinary();

        return GeneralizedTime::fromBinary($binary);
    }

    /**
     * @return MessageImprint
     * @throws Exception
     */
    public function getMessageImprint()
    {
        return new MessageImprint($this->object->findChildrenByType(Sequence::class)[0]);
    }

    /**
     * @return Integer|ASN1ObjectInterface
     * @throws Exception
     */
    public function getNonce()
    {
        $integers = $this->object->findChildrenByType(Integer::class);
        if (count($integers) == 3) {
            $binary = $integers[2]->getBinary();

            return Integer::fromBinary($binary);
        }

        return null;
    }

    /**
     * @return Boolean|null
     * @throws Exception
     */
    public function getOrdering()
    {
        /** @var Boolean[] $booleans */
        $booleans = $this->object->findChildrenByType(Boolean::class);
        if (count($booleans)) {
            $binary = $booleans[0]->getBinary();

            return Boolean::fromBinary($binary);
        }

        return null;
    }

    /**
     * @return ObjectIdentifier|ASN1ObjectInterface
     * @throws Exception
     */
    public function getPolicy()
    {
        $binary = $this->object->findChildrenByType(ObjectIdentifier::class)[0]->getBinary();

        return ObjectIdentifier::fromBinary($binary);
    }

    /**
     * @return Integer|ASN1ObjectInterface
     * @throws Exception
     */
    public function getSerialNumber()
    {
        $binary = $this->object->findChildrenByType(Integer::class)[1]->getBinary();

        return Integer::fromBinary($binary);
    }

    /**
     * TODO: create CMS and check correctness
     *
     * @return GeneralName|null
     * @throws Exception
     */
    public function getTsa()
    {
        /** @var ExplicitlyTaggedObject[] $explicits */
        $explicits = $this->object->findChildrenByType(ExplicitlyTaggedObject::class);
        foreach ($explicits as $explicit) {
            if ($explicit->getIdentifier()->getTagNumber() == 0) {
                return new GeneralName($explicit);
            }
        }

        return null;
    }
}
