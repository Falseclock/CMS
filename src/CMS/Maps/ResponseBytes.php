<?php
/**
 * ResponseBytes
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Adapik/CMS
 */

namespace Adapik\CMS\Maps;

use FG\ASN1\Identifier;

abstract class ResponseBytes
{
    /**
     * ResponseBytes ::=       SEQUENCE {
     *        responseType   OBJECT IDENTIFIER,
     *        response       OCTET STRING }
     */
    const MAP = [
        'type' => Identifier::SEQUENCE,
        'children' => [
            'responseType' => ['type' => Identifier::OBJECT_IDENTIFIER],
            'response' => ['type' => Identifier::OCTETSTRING, 'children' => BasicOCSPResponse::MAP],
        ],
    ];
}
