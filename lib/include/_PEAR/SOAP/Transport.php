<?php
/**
 * This file contains the code for an abstract transport layer.
 *
 * PHP versions 4 and 5
 *
 * LICENSE: This source file is subject to version 2.02 of the PHP license,
 * that is bundled with this package in the file LICENSE, and is available at
 * through the world-wide-web at http://www.php.net/license/2_02.txt.  If you
 * did not receive a copy of the PHP license and are unable to obtain it
 * through the world-wide-web, please send a note to license@php.net so we can
 * mail you a copy immediately.
 *
 * @category   Web Services
 * @package    SOAP
 * @author     Dietrich Ayala <dietrich@ganx4.com>
 * @author     Shane Caraveo <Shane@Caraveo.com>
 * @copyright  2003-2005 The PHP Group
 * @license    http://www.php.net/license/2_02.txt  PHP License 2.02
 * @link       http://pear.php.net/package/SOAP
 */

require_once PEAR_PATH.'SOAP/Base.php';

/**
 * SOAP Transport Layer
 *
 * This layer can use different protocols dependant on the endpoint url provided
 * no knowlege of the SOAP protocol is available at this level
 * no knowlege of the transport protocols is available at this level
 *
 * @access   public
 * @package  SOAP
 * @author   Shane Caraveo <shane@php.net>
 */
class SOAP_Transport
{
    function &getTransport($url, $encoding = SOAP_DEFAULT_ENCODING)
    {
        $urlparts = @parse_url($url);

        if (!$urlparts['scheme']) {
            $fault = SOAP_Base_Object::_raiseSoapFault("Invalid transport URI: $url");
            return $fault;
        }

        if (strcasecmp($urlparts['scheme'], 'mailto') == 0) {
            $transport_type = 'SMTP';
        } elseif (strcasecmp($urlparts['scheme'], 'https') == 0) {
            $transport_type = 'HTTP';
        } else {
            /* handle other transport types */
            $transport_type = strtoupper($urlparts['scheme']);
        }
        $transport_include = PEAR_PATH.'SOAP/Transport/' . $transport_type . '.php';
        $res = @include_once($transport_include);
        if (!$res && !in_array($transport_include, get_included_files())) {
            $fault = SOAP_Base_Object::_raiseSoapFault("No Transport for {$urlparts['scheme']}");
            return $fault;
        }
        $transport_class = "SOAP_Transport_$transport_type";
        if (!class_exists($transport_class)) {
            $fault = SOAP_Base_Object::_raiseSoapFault("No Transport class $transport_class");
            return $fault;
        }
        $t =& new $transport_class($url, $encoding);

        return $t;
    }

}
