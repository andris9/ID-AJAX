<?php

error_reporting(E_ALL & ~(E_STRICT|E_NOTICE|E_WARNING));

session_start();
require_once("lib/api.php");
require_once("filestore.php");

Header("Content-type: text/javascript; Charset=utf-8");
$response = array();

/*
 * URLHandlers
 * ===========
 *
 * Kõik vastused on JSON formaadis (v.a. getDDOCHandler).
 *
 * Vigade korral on kasutusel struktuur
 * {status:"ERROR", message:"Vea kirjeldus", code="vea_identifikaator"}
 */

class URLHandlers{
	/*
     * mobileAuthRequestHandler
     * ------------------------
     *
     * Algatab Mobiil-ID autentimise
     *
     * GET:
     * action=mobileAuthRequest&phone={PHONENR}[&message={MESSAGE}][&lang={LANG}]
     *
     * - PHONENR on telefoni number
     * - MESSAGE telefoni ekraanile väljastatav täiendav sõnum
     * - LANG [(EST), ENG, RUS] keelevalik
     *
     * Õnnestumise korral väljastab
     * {status:"OK", sid:"{SID}", code:"{CHALLENGE}"}
     *
     * - SID on sessiooni ID, mida tuleb kasutada autentimise staatuse kontrollimisel
     *   tegevusega mobileAuthStatus
     * - CHALLENGE on neljakohaline number, mis kuvatakse kasutaja telefoni ekraanil
     *   kasutaja peab veenduma nende numbrite samasuses
     *
     * Ebaõnnestumise korral
     * {status:"ERROR", message:"{ERROMESSAGE}", code:"{ERRORCODE}"}
     *
     * Võimalikud vea allikad:
     * - telefoni number puudub või on vigane
     * - telefoni number ei ole Mobiil-ID teenusega liitunud, levist väljas vms ühendusvusprobleemid
     *
	 */
    public static function mobileAuthRequestHandler(){
    	$sid = Auth::MobileAuthRequest($_GET["phone"],$_GET["message"],$_GET["lang"]);
        if(!$sid){
            $response["status"] = "ERROR";
            $response["message"] = Auth::$error;
            $response["code"] = Auth::$error_code;
        }else{
            $response["status"] = "OK";
            $response["sid"] = Auth::$sid;
            $response["code"] = Auth::$data["ChallengeID"];
        }
        echo json_encode($response);
        exit;
    }

    /*
     * mobileAuthStatusHandler
     * ------------------------
     *
     * Kontrollib Mobiil-ID autentimise kulgu
     *
     * GET:
     * action=mobileAuthStatus&sid={SID}
     *
     * Kui autentimine pole veel lõppenud, väljastab
     * {status:"WAITING"}
     *
     * Kui autentimine õnnestus, väljastab
     * {status:"AUTHENTICATED", data:{USERDATA}}
     *
     * - USERDATA on objekt kasutaja andmetega, mis sisaldab järgmisi võtmeid
     *   - UserIDCode Kasutaja ID kood
     *   - UserGivenname eesnimi
     *   - UserSurname perekonnanimi
     *   - UserCountry maatähis (EE)
     *
     * Ebaõnnestumise korral tagastatakse
     * {status:"ERROR", message:"{ERROMESSAGE}", code:"{ERRORCODE}"}
     *
     * Võimalikud vea allikad:
     * - sessiooni võti on määramata või see ei kehti
     *
     */
    public static function mobileAuthStatusHandler(){
    	Auth::MobileAuthStatus($_GET["sid"]);
        if(Auth::$error){
            $response["status"] = "ERROR";
            $response["message"] = Auth::$error;
            $response["code"] = Auth::$error_code;
        }else if(Auth::$stage == "authenticated"){
            $response["status"] = "AUTHENTICATED";
            $response["data"] = array(
                "UserIDCode"    => Auth::$data["UserIDCode"],
                "UserGivenname" => Auth::$data["UserGivenname"],
                "UserSurname"   => Auth::$data["UserSurname"],
                "UserCountry"   => Auth::$data["UserCountry"]
            );
        }else if(Auth::$stage == "progress"){
            $response["status"] = "WAITING";
        }else{
            $response["status"] = "ERROR";
            $response["message"] = "Unknown error";
            $response["code"] = "PHONE_UNKNOWN";
        }
        echo json_encode($response);
        exit;
    }

    /*
     * mobileAuthLogoutHandler
     * -----------------------
     *
     * Logib kasutaja välja.
     *
     * GET:
     * action=mobileAuthLogout
     *
     * Tagastag igal juhul
     * {status:"LOGGED_OUT"}
     *
     */
    public static function mobileAuthLogoutHandler(){
    	Auth::Logout();
        $response["status"] = "LOGGED_OUT";
        echo json_encode($response);
        exit;
    }

    /*
     * addFileHandler
     * --------------
     *
     * Võtab vastu faili ning salvestab kettale. Failide asukoht on kirjas
     * konfiguratsioonifaili konstandis FILESTORE_DIRECTORY
     *
     * GET:
     * action=addFile[&contents={FILE}][&filename={FILENAME}]
     *
     * POST:
     * [&contents={FILE}][&filename={FILENAME}]
     *
     * - FILE on faili sisu. Hetkel on tegu stringi, aga mitte multipart vormi
     *   kaudu laetud failiga
     * - FILENAME on faili nimi
     *
     * Kui fail on edukalt üles laetud, on vastuseks
     * {status:"OK", fid:"{FID}"}
     *
     * - FID on unikaalne 42 baidine faili identifikaator
     *
     * Ebaõnnestumise korral tagastatakse
     * {status:"ERROR", message:"{ERROMESSAGE}", code:"{ERRORCODE}"}
     *
     * Võimalikud vea allikad:
     * - failide üleslaadimine on ära keelatud
     * - Kasutaja pole sisse loginud
     * - Faili sisu puudub
     * - Probleemid faili salvestamisel kettale
     *
     */
    public static function addFileHandler(){
        if(!FILESTORE_ALLOW_UPLOADS){
        	$response["status"] = "ERROR";
            $response["message"] = "This feature is disabled";
            $response["code"] = "NOT_ALLOWED";
            echo json_encode($response);
            exit;
        }

    	if(!Auth::AuthStatus()){
            $response["status"] = "ERROR";
            $response["message"] = "You need to be authenticated";
            $response["code"] = "PHONE_NOT_AUTHENTICATED";
            echo json_encode($response);
            exit;
        }

        $message = $_REQUEST["contents"];
        $fname = trim($_REQUEST["filename"]);

        if(!$message){
            $response["status"] = "ERROR";
            $response["message"] = "No file provided";
            $response["code"] = "EMPTY_FILE";
        }else{
            // salvesta fail ja tagasta FID
            $fid = FileStore::add($message, $fname);
            if($fid){
                $response["status"] = "OK";
                $response["FID"] = $fid;
            }else{
                $response["status"] = "ERROR";
                $response["message"] = "Error saving file";
                $response["code"] = "FILE_ERROR";
            }
        }

        echo json_encode($response);
        exit;
    }

    /*
     * getDDOCHandler
     * --------------
     *
     * Saadab brauserile allalaadimiseks .DDOC faili
     *
     * GET:
     * action=getDDOC&fid={FID}
     *
     * - FID on faili 42 baidine identifikaator
     *
     * Vea korral väljastatakse veateade "404 Not Found"
     *
     * Võimalikud vea allikad:
     * - FID on puudu või vale
     *
     */
    public static function getDDOCHandler(){
    	FileStore::downloadDDOC($_GET["fid"]);
        exit;
    }

    /*
     * mobileSignRequestHandler
     * ------------------------
     *
     * Algatab Mobiil-ID põhise digiallkirjastamise
     *
     * GET:
     * action=mobileSignRequest&phone={PHONENR}&fid={FID}[&message={MESSAGE}][&lang={LANG}]
     *
     * - PHONENR on telefoni number
     * - MESSAGE telefoni ekraanile väljastatav täiendav sõnum
     * - LANG [(EST), ENG, RUS] keelevalik
     *
     * Õnnestumise korral väljastab
     * {status:"OK", sid:"{SID}", code:"{CHALLENGE}"}
     *
     * - SID on sessiooni ID, mida tuleb kasutada allkirjastamise staatuse kontrollimisel
     *   tegevusega mobileSignStatus
     * - CHALLENGE on neljakohaline number, mis kuvatakse kasutaja telefoni ekraanil
     *   kasutaja peab veenduma nende numbrite samasuses
     *
     * Ebaõnnestumise korral
     * {status:"ERROR", message:"{ERROMESSAGE}", code:"{ERRORCODE}"}
     *
     * Võimalikud vea allikad:
     * - kasutaja pole eelnevalt sisse loginud
     * - telefoni number puudub või on vigane
     * - telefoni number ei ole Mobiil-ID teenusega liitunud, levist väljas vms ühendusvusprobleemid
     * - FID on puudu või vigane
     *
     */
    public static function mobileSignRequestHandler(){
    	if(!Auth::AuthStatus() || !Auth::$data["PhoneNumber"]){
            $response["status"] = "ERROR";
            $response["message"] = "You need to be authenticated";
            $response["code"] = "NOT_AUTHENTICATED";
            echo json_encode($response);
            exit;
        }

        $fileData = FileStore::retrieve($_GET["fid"]);
        if(!$fileData){
            $response["status"] = "ERROR";
            $response["message"] = "Unknown file";
            $response["code"] = "FILE_INVALID";
            echo json_encode($response);
            exit;
        }

        Sign::addFile($fileData["contents"], $fileData["fileName"]);
        $sid = Sign::MobileSignRequest(Auth::$data["PhoneNumber"], $_GET["fid"], $_GET["message"], $_GET["lang"], count($fileData["signatures"]));
        if(!$sid){
            $response["status"] = "ERROR";
            $response["message"] = Sign::$error;
            $response["code"] = Sign::$error_code;
        }else{
            $response["status"] = "OK";
            $response["sid"] = Sign::$sid;
            $response["code"] = Sign::$data["ChallengeID"];
        }
        echo json_encode($response);
        exit;
    }

    /*
     * mobileSignStatusHandler
     * ------------------------
     *
     * Kontrollib Mobiil-ID allkirjastamise kulgu
     *
     * GET:
     * action=mobileSignStatus&sid={SID}
     *
     * Kui allkirjastamine pole veel lõppenud, väljastab
     * {status:"WAITING"}
     *
     * Kui allkirjastamine õnnestus, väljastab
     * {status:"SIGNED", fid:"{FID}"}
     *
     * - FID on faili 42 bitine identifikaator
     *
     * Ebaõnnestumise korral tagastatakse
     * {status:"ERROR", message:"{ERROMESSAGE}", code:"{ERRORCODE}"}
     *
     * Võimalikud vea allikad:
     * - sessiooni võti on määramata või see ei kehti
     *
     */
    public static function mobileSignStatusHandler(){
    	if(!Auth::AuthStatus() || !Auth::$data["PhoneNumber"]){
            $response["status"] = "ERROR";
            $response["message"] = "You need to be authenticated";
            $response["code"] = "NOT_AUTHENTICATED";
            echo json_encode($response);
            exit;
        }

        Sign::MobileSignStatus($_GET["sid"]);
        if(Sign::$error){
            $response["status"] = "ERROR";
            $response["message"] = Sign::$error;
            $response["code"] = Sign::$error_code;
        }else if(Sign::$stage == "signed"){
            $response["status"] = "SIGNED";

            $fileData = FileStore::retrieve(Sign::$data["FID"]);
            Sign::addFile($fileData["contents"], $fileData["fileName"], $fileData["mimeType"]);

            if(!FileStore::addSignature(Sign::$data["FID"], Sign::$data["Signature"])){
                $response["status"] = "ERROR";
                $response["message"] = "Adding signature to the file failed";
                $response["code"] = "FILE_ERROR";
                unset($_SESSION["Sign_Data"]);
            }else{
                $response["FID"] = Sign::$data["FID"];
            }
        }else if(Sign::$stage == "signed_cached"){
            $response["status"] = "SIGNED";
            $response["FID"] = Sign::$data["FID"];
        }else if(Sign::$stage == "progress"){
            $response["status"] = "WAITING";
        }else{
            $response["status"] = "ERROR";
            $response["message"] = "Unknown error";
            $response["code"] = "UNKNOWN_ERROR";
        }
        echo json_encode($response);
        exit;
    }

    /*
     * defaultHandler
     * --------------
     *
     * Kasutatakse juhul, kui proovitakse kasutada tundmatud teenust
     *
     * Tagastatab
     * {status:"ERROR", message:"Unknown method", code:"UNKNOWN_METHOD"}
     */
    public static function defaultHandler(){
    	$response["status"] = "ERROR";
        $response["message"] = "Unknown method";
        $response["code"] = "UNKNOWN_METHOD";
        echo json_encode($response);
        exit;
    }


    public static function cardPrepareSignatureHandler(){
    	if(!Auth::AuthStatus() || !Auth::$data["UseIDCard"]){
            $response["status"] = "ERROR";
            $response["message"] = "You need to be authenticated";
            $response["code"] = "NOT_AUTHENTICATED";
            echo json_encode($response);
            exit;
        }

        $fileId = $_REQUEST["fileId"];
        $certId = $_REQUEST["certId"];
        $certHex = $_REQUEST["certHex"];

        if(!$fileId){
            $response["status"] = "ERROR";
            $response["message"] = "No file selected";
            $response["code"] = "NO_FILE";
            echo json_encode($response);
            exit;
        }

        if(!$certId || !$certHex){
            $response["status"] = "ERROR";
            $response["message"] = "No certificate provided";
            $response["code"] = "NO_CERT";
            echo json_encode($response);
            exit;
        }

        $sid = Sign::CardPrepareSignature($fileId, $certId, $certHex);
        if(!$sid){
            $response["status"] = "ERROR";
            $response["message"] = Sign::$error;
            $response["code"] = Sign::$error_code;
        }else{
            $response["status"] = "OK";
            $response["signatureRequest"] = Sign::$data["signatureRequest"];
            $response["signatureId"] = Sign::$data["signatureId"];
        }
        echo json_encode($response);
        exit;

    }


    public static function cardFinalizeSignatureHandler(){
        if(!Auth::AuthStatus() || !Auth::$data["UseIDCard"]){
            $response["status"] = "ERROR";
            $response["message"] = "You need to be authenticated";
            $response["code"] = "NOT_AUTHENTICATED";
            echo json_encode($response);
            exit;
        }

        $sid = Sign::$data["sid"];
        $signatureId = $_REQUEST["signatureId"];
        $signatureHex = $_REQUEST["signatureHex"];

        if(!$signatureId || !$signatureHex){
            $response["status"] = "ERROR";
            $response["message"] = "No signature provided";
            $response["code"] = "NO_SIGNATURE";
            echo json_encode($response);
            exit;
        }

        $success = Sign::CardFinalizeSignature($signatureId, $signatureHex);
        if(!$success){
            $response["status"] = "ERROR";
            $response["message"] = Sign::$error;
            $response["code"] = Sign::$error_code;
        }else{

            $response["status"] = "SIGNED";

            $fileData = FileStore::retrieve(Sign::$data["FID"]);
            Sign::addFile($fileData["contents"], $fileData["fileName"], $fileData["mimeType"]);

            if(!FileStore::addSignature(Sign::$data["FID"], Sign::$data["Signature"])){
                $response["status"] = "ERROR";
                $response["message"] = "Adding signature to the file failed";
                $response["code"] = "FILE_ERROR";
                unset($_SESSION["Sign_Data"]);
            }else{
                $response["FID"] = Sign::$data["FID"];
            }

        }
        echo json_encode($response);
        exit;

    }
}

switch($_GET["action"]){

    /***** AUTH *****/
	case "mobileAuthRequest":
        URLHandlers::mobileAuthRequestHandler();
        break;
    case "mobileAuthStatus":
        URLHandlers::mobileAuthStatusHandler();
        break;
    case "mobileAuthLogout":
        URLHandlers::mobileAuthLogoutHandler();
        break;

    /***** SIGN *****/
    case "addFile":
        URLHandlers::addFileHandler();
        break;
    case "getDDOC":
        URLHandlers::getDDOCHandler();
        break;
    case "mobileSignRequest":
        URLHandlers::mobileSignRequestHandler();
        break;
    case "mobileSignStatus":
        URLHandlers::mobileSignStatusHandler();
        break;
    case "cardPrepareSignature":
        URLHandlers::cardPrepareSignatureHandler();
    case "cardFinalizeSignature":
        URLHandlers::cardFinalizeSignatureHandler();
    default:
        URLHandlers::defaultHandler();

}


?>