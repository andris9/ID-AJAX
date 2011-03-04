<?

define('DD_SERVICE_NAME', 'Testimine');
define('FILESTORE_ALLOW_UPLOADS', true);


/**
 * SOAP_Client
 *
 * kasutusel ühenduse pidamiseks SOAP-serveriga ja ühenduseks
 * vajalike xml-de genereerimiseks.
 * @copyright   http://pear.php.net/package/SOAP
 */

define('PEAR_PATH', dirname(__FILE__).'/lib/include/_PEAR/'); // Kasutamaks süsteemseid PEARi mooduleid, väärtusta see ''

//define('PEAR_PATH', '');

require_once PEAR_PATH.'SOAP/Client.php';


/**
 * XML_Unserializer
 *
 * kasutusel SOAP Serveri saadetud xml-vastuste töötlemiseks
 */
require_once PEAR_PATH.'XML/Unserializer.php';

/**
* PEAR vigade suunamine (globaalne funktsioon)
* tanel
*/
if (function_exists('raise_error'))
    PEAR::setErrorHandling(PEAR_ERROR_CALLBACK, 'raise_error');

/**
 * DigiDocService endpoint URL
 */
define('DDS_ENDPOINT', 'https://www.openxades.org:8443');

/**
 * DigiDocService WSDL
 */
define('DD_WSDL', 'https://www.openxades.org:8443/?wsdl');

# DigiDocService certifcate issuer certificate file
define('DD_SERVER_CA_FILE', dirname(__FILE__).'/lib/service_certs.pem');

/**#@+
 * Serveriga ühenduse loomiseks vajalik parameeter
 */
define('DD_PROXY_HOST', '');
define('DD_PROXY_PORT', '');
define('DD_PROXY_USER', '');
define('DD_PROXY_PASS', '');
define('DD_TIMEOUT', '9000');
/**#@-*/

/**
 * WSDL classi lokaalse faili nimi
 *
 * Selles hoitakse WSDL-i alusel genereeritud PHP classi,
 * et ei peaks iga kord seda serverist uuesti pärima.
 * Kui WSDL faili aadressi muuta, tuleb ka see fail ära kustutada, kuna
 * selles hoitakse ka serveri aadressi, mis pärast muutmist enam ei ühti
 * õige aadressiga!
 */
define('DD_WSDL_FILE', dirname(__FILE__).'/wsdl.class.php');

/*
 * NB! Uue wsdl.class.php faili genereerimisel tuleb muuta
 * WebService_DigiDocService_DigiDocService funktsioon järgmisele kujule:
 *
 *     function WebService_DigiDocService_DigiDocService()
 *     {
 *         $this->SOAP_Client(DDS_ENDPOINT, 0, 0,
 *              array('curl' => array('64' => '1', '81' => '2', '10065' => DD_SERVER_CA_FILE)));
 *     }
 */

/**
 * Vaikimis kasutatav keel
 * Võimalikud väärtused: EST / ENG / RUS
 */
define('DD_DEF_LANG', 'EST');

define('LOCAL_FILES', TRUE);

?>