<?php

/**
 * DigestInfo
 *
 * PHP version 5
 *
 * @category  File
 * @package   ASN1
 * @author    Jim Wigginton <terrafrost@php.net>
 * @copyright 2016 Jim Wigginton
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 * @link      http://phpseclib.sourceforge.net
 */

namespace FG\ASN1\Maps;

use FG\ASN1\Identifier;

/**
 * DigestInfo
 *
 * from https://tools.ietf.org/html/rfc2898#appendix-A.3
 *
 * @package ASN1
 * @author  Jim Wigginton <terrafrost@php.net>
 * @access  public
 */
abstract class DigestInfo
{
    const MAP = [
        'type'     => Identifier::SEQUENCE,
        'children' => [
            'digestAlgorithm' => AlgorithmIdentifier::MAP,
            'digest' => ['type' => Identifier::OCTET_STRING]
        ]
    ];
}
