<?php

/**
 * DigiDoc PHP klient
 *
 * Paketi kasutamiseks on kaks võimalust, kas läbi WSDL-st genereeritud 
 * objekti meetodite klassiga <b>Base_DigiDoc</b> või kasutades <b>DigiDoc</b> 
 * klassi poolt pakutavaid vahendusmeetodeid.
 * Esimene meetod võimaldab rohkem kasutajal määrata, kuidas teostada 
 * mingit tegevust ja mis moodi toimub infovahetus kasutaja ja DigiDoc
 * teenuse vahel.
 * Teise variandi puhul on enamus tegevusi automatiseeritud ja tulemused
 * tagastatakse juba töödeldud kujul valmis kohe kasutamiseks koodis.
 * 
 * <pre>
 * $dd = new Base_DigiDoc( ); <i># Initsialiseerime klassi.</i>
 * <i>// Pead alati enne funktsiooni kasutama addHeader() sess.koodi lisamiseks</i> 
 * $dd->addHeader( array('SessionCode' => $SESSION_CODE) );
 * <i>// Küsime nüüd näiteks dokumendi infot</i>
 * $result = $dd -> WSDL -> GetSignedDocInfo();
 * <i>// Väljastame tulemuse kasutades selleks DigiDoc klassi meetodit </i>
 * $dd->__DDebug__( $result );
 * </pre>
 *
 *
 * Järgmine näide siis sama funktsiooni kasutamise kohta juba DigiDoc
 * klassi meetodeid kasutades. <i>momendil on see osa veel realiseerimisel</i>
 *
 * <pre>
 * $dd = new DigiDoc(); <i># Initsialiseerime DigiDoc klassi.</i>
 * $result = $dd->GetSignedDocInfo();
 * $dd->__DDebug__( $result ); <i># ja väljastame taas tulemuse</i>
 * </pre>
 *
 *
 * Miinimumnõuded klassi tööks
 * - <b>PHP 4.3.4</b> vļæ½i uuem
 * - <i>kui soov kasutada HTTPS ühendust siis lisaks ka CURL-extension</i>
 * - <i>sessionite kasutamine peab olema lubatud</i>
 *
 * - <b>PHP PEAR</b> moodulid korrektselt installitud
 * <i>Lisaks veel allolevad PEAR moodulid koos neile vajalike moodulitega.
 * Kui PEAR on korralikult installitud, siis saab pakette installida käsuga
 * $> pear install {package-name}
 * Võib lisada veel install järele võtme <b>-a</b>, mis installib automaatselt 
 * ka kõik sõltuvuse rahuldamiseks vajalikud paketid. Ei toimi kõikide PEAR 
 * versioonidega.
 * Soovitav oleks enne määrata ära PEAR-i confis nii proxy andmed, kui ka seada
 * eelistatavaks paketi olekuks beeta, kuna kahjuks kõik vajalikud paketid pole 
 * veel 'stable' staatuses.
 * <kbd>pear config-set preferred_state beta</kbd>
 * Konfiguratsiooni vaatamiseks:
 * <kbd>$> pear config-show</kbd>
 * Igat parameetrit saab muuta config-set võtmega. Vaata oleku muutmise näidet.</i>
 * - SOAP
 * $> pear install -a SOAP
 * - XML_Serializer
 * $> pear install -a XML_Serializer
 *
 *
 * - <b>Browser</b>
 * - MS Internet Explorer 5.x või uuem
 * - Mozilla põhine brauser (Win32 vļæ½i Linux)
 * - Browseril vajalikud draiverid installitud ID-kaardi ja DigiDoc-ga
 *   tööks. Vajalik tarkvara on leitav ID-Kaardi ametlikul kodulehel
 *   http://www.id.ee/installer
 *
 *
 * Selleks et kasutada klassi poolt kasutatavaid meetodid toimiks edukalt
 * on ette antud mõned kriteeriumid, mis peavad olema täidetud:
 * - Failide saatmine
 * - <i>File INPUT välja nimi "ddoc" - kasutatakse DigiDoc faili saatmiseks</i>
 * - <i>File INPUT välja nimi "file" - kasutatakse failide lisamiseks</i>
 *
 *
 * Klass lubab teostada kõiki WSDL failiga kirjeldatud operatsioone.
 * - Alustada sessiooni digidoc faili alusel
 * - Alustada sessiooni luues uue digidoc faili saadetud tavafailile
 * - Alustada sessiooni luues uue tühja digidoc faili
 * - Lisada allkirju
 * - Kustutada allkirju
 * - Lisada faile <i>(ainult, kui pole ühtegi allkirja)</i>
 * - Kustutada faile <i>(ainult, kui pole ühtegi allkirja)</i>
 * - Salvestada faile
 * - Salvestada allkirja sertifikaati
 * - Salvestada kinnitaja sertifikaati
 * - Salvestada kinnitus
 * - Salvestada kogu digidoci
 * - Vaadata digidoci infot
 *
 *
 * <b>Installeerimine</b>
 *
 * <b>1.</b> Installeerida korralikult kõik vajalikud PEAR moodulid ja nende 
 * tööks vajalikud lisa moodulid. Kontrollida ka et <i>php.ini</i> failis olev
 * <i>include_path</i> viitaks PEAR juurkaustale, et PEAR moodulid oleks alati 
 * webserveris töötavatele PHP skrptidele kättesaadavad. Täpsemad õpetused 
 * selleks leiab nii PHP (http://www.php.net) kui PEAR (http://pear.php.net)
 * kodulehtedelt.
 * DigiDoc-i jaoks vajalikud PEAR moodulid on:
 * - <b>SOAP</b>     [http://pear.php.net/packages/SOAP]
 * - <b>XML_Serializer</b> [http://pear.php.net/package/XML_Serializer]
 * Mõlemad on <i>beta</i> staatuses. Selleks et neid pear installeriga saaks 
 * installida, peab pearinstalleri "preferred_state" parameetri väärtuseks 
 * olema beta
 *
 * <b>2.</b> Muuta faili DigiDoc.class.php faili alguses olevad parameetrid 
 * oma süsteemile sobivaks. Kui kaustas esineb fail <i>wsdl.class.php</i>, 
 * siis on see soovitav pärast parameetrite muutmist alati kustutada!
 *
 * <b>3.</b>Installeerida ID-Kaardiga tööks vajalikud programmid ja moodulid
 * süsteemi ja häälestada ka brauserid ID-kaardi kasutamisek.
 * 
 * <b>4.</b>Edasi võib juba käivitada näidisrakenduse <i>index.php</i>. Kui
 * kõik on korras, peaks ilmuma digidoci loomise variantidega ekraan.
 *
 * @todo        lisada klass DigiDoc, mis annaks kasutajale lihtsustatud 
 * juurdepääsu DigiDoc süsteemi funktsioonidele.
 *
 * @package     DigiDoc
 * @author		Roomet Kirotarp <Roomet.Kirotarp@hot.ee>
 * @version		0.0.8 2004.05.20 : Esimene versioon.
 * @version		1.0.0 2004.06.01 : Beeta, kõik funktsioonid realiseeritud.
 * @version		1.1.0 2004.06.03 : Tükeldatud mitmesse klassi.
 * @version		2.0.0 2004.06.16 : Parandatud ja täiendatud versioon.
 * @version		2.0.1 2004.07.20 : Kõrvaldatud hash-i BASE64 mittekodeerimise viga, eemaldatud allalaetavatel failidel UTF8-ks kodeerimine.
 * @version		2.0.2 2004.07.21 : Suurte (>7MB) failide korral ei toiminud varasemalt failid BASE64 data lisamine DigiDoc-i.
 * @version		2.0.3 2004.07.21 : Eesti susisevate häälikute korrektne töötlemine UTF-8 kodeeringuga failinnimes ja allkirjastamisega seotud väljadel (func Parser_DigiDoc::getDigiDoc()).
 * @version		2.0.5 2005.12.07 : Hashi arvutamist muudetud, enne räsi arvutamist viiakse andmefailis reavahetus \n kujule
* @version		2.0.6 2006.07.26 :
									- Uuendatud PEAR::SOAP pakette, kasutuses versioon 0.9.4
									- Lisatud teenuse sertifikaadi verifitseerimine, sertifikaatide fail on määratav config faili parameetriga DD_SERVER_CA_FILE
									-
 */



###########################################################################
###########################################################################
###########################################################################
/**
 * Digidoc kliendi lihtsustatud liide.
 *
 * DigiDoc kliendi lihtsustatu liides, mis annab lihtsustadu juurdepääsu 
 * kõikidele funktsioonidele!
 * @package       DigiDoc
 */
class DigiDoc{
    
	
	/**
	 * Constructor
	 */
	function DigiDoc(){
	    
	} // end func

} // end class



###########################################################################
###########################################################################
###########################################################################
/**
 * DigiDoc klass
 *
 * Klass DigiDoc teenuse kasutamiseks. Sisaldab vajalikke meetodeid 
 * infovahetuse pidamiseks DigiDoc teenust pakkuva serveriga.
 * 
 * @category	SOAP
 * @package		DigiDoc
 * @version		1.0.0
 * @author		Roomet Kirotarp <Roomet.Kirotarp@hot.ee>
 * @since		2004.05.01
 * @access		public
 */
class Base_DigiDoc {

	/**
	 * Soap kliendi ühenduse objekt
	 */
	var $Client;

	/**
	 * WSDL faili põhjal genereeritud liides
	 */
	var $WSDL;

	/**
	 * Brauseri ja OS-i andmed
	 */
	var $browser;


	/*
	 * funktsioon class WebService_DigiDocService_DigiDocService definitsiooni
	 * laadimiseks _enne_ sessiooni alustamist et oleks võimalik Base_DigiDoc
	 * sessiooni salvestada
	 */
	function load_WSDL()
	{
            //2010.07, ahto, parandusettepanek by Anttix
            $connection['curl'] = array(
			CURLOPT_SSL_VERIFYPEER => 1,    //1 (see http://curl.haxx.se/libcurl/c/curl_easy_setopt.html)
			CURLOPT_SSL_VERIFYHOST => 2,    //2
                CURLOPT_CAINFO => DD_SERVER_CA_FILE
            );        

            if(is_readable( DD_WSDL_FILE ) && filesize( DD_WSDL_FILE ) > 32){
                    include_once DD_WSDL_FILE;
            } else {
                    $wsdl = new SOAP_WSDL( DD_WSDL, $connection );
                    $wcode = $wsdl->generateProxyCode();
                    eval( $wcode );
                    File::saveLocalFile( DD_WSDL_FILE, "<?php\n".$wcode."\n?".">");	
            } 
	}
	
	/**
	 * Constructor
	 */
	function Base_DigiDoc() {
		$connection = $this->getConnect();
		$this->Client = new SOAP_Client ( DD_WSDL, TRUE, FALSE, $connection);
		
		$this->WSDL = new WebService_DigiDocService_DigiDocService();
				
		$this->browser = File::getBrowser();
		
                //2010.07, ahto, parandus
                //$this->NS = $this->Client->_wsdl->definition['targetNamespace'];
                $this->NS = isset($this->Client->_wsdl->definition['targetNamespace']) ? $this->Client->_wsdl->definition['targetNamespace'] : "";

	} //function


	/**
	 * Lisab vastava parameetri ja väärtuse SOAP headerisse
	 *
	 * Parameetri lisamiseks SOAP serverile saadetavatesse XML päringuisse.
	 * Antud juhul enamasti sessiooni koodi lisamiseks, et tuvastada õige
	 * digidoc failiga tegelemist.
	 *
	 * <code>
	 * $dd->addHeader('SessionCode', '01223121');
	 * </code>
	 * <code>
	 * $x = array('SessionCode' => '123423423234', 'testVar'=>'muutuja');
	 * $dd->addHeader($x);
	 * </code>
	 * 
	 * @param     mixed    $var     Päisesse lisatavad parameetrid
	 * @param     mixed    $value   ühe muutuja lisamisel, selle väärtus
	 * @access    public
	 * @return    array
	 */
	function addHeader($var, $value=null){
		if(is_array($var)){
			while(list($key, $val) = each($var)){
				$hr = new SOAP_Header($key, NULL, $val, FALSE, FALSE);
				$hr->namespace = $this->NS;
				if(isset($hr->attributes['SOAP-ENV:actor'])) unset($hr->attributes['SOAP-ENV:actor']);
				if(isset($hr->attributes['SOAP-ENV:mustUnderstand'])) unset($hr->attributes['SOAP-ENV:mustUnderstand']);
				$this->WSDL->addHeader($hr);
			} //while
			return TRUE;
		} elseif($var && $value) {
			$hr = new SOAP_Header($var, NULL, $value, FALSE, FALSE);
			$hr->namespace = $this->NS;
			if(isset($hr->attributes['SOAP-ENV:actor'])) unset($hr->attributes['SOAP-ENV:actor']);
			if(isset($hr->attributes['SOAP-ENV:mustUnderstand'])) unset($hr->attributes['SOAP-ENV:mustUnderstand']);
			$this->WSDL->addHeader($hr);
		} else {
			return FALSE;
		} //else
	}

	
	/**
	 * Tagastab vastuvõetud DigiDoci formaadi ja versiooni
	 * @return	array
	 */
	function getDigiDocArray(){
		$us = new XML_Unserializer();
		$us->unserialize($this->WSDL->xml, FALSE);
		$xml = $us->getUnserializedData();
		return $xml;
	} //function

	/**
	 * Puhastab saadetud kuupäeva ülearustest sümbolitest
	 * @access	private
	 */
	function cleanDateString($date){
		return preg_replace("'[TZ]'"," ",$date);
	} //function

	
	/**
	 * Sertifikaadi salvestamine
	 */
	function saveCertAs($file){
		$filename = uniqid('certificate').'.cer';
		$content = "-----BEGIN CERTIFICATE-----\n".$file."\n-----END CERTIFICATE-----\n";
		File::SaveAs($filename, $content, 'application/certificate', 'utf-8');
	} //function


	/**
	 * Kehtivuskinnituse salvestamine
	 */
	function saveNotaryAs($file){
		$filename = uniqid('ocsp').'.ocsp';
		$content = base64_decode($file);
		File::SaveAs($filename, $content, 'application/notary-ocsp', 'utf-8');
	} //function

	
	/**
	 * Tagastab ddociga kaasas olnud andmefailid array-na
	 */
	function getDataFiles($result){
		$res = array();
		return $res;
	} //function


	
	/**
	 * Tagastab ddociga kaasas olnud allkirjad array-na
	 */
	function getSignatures($result){
		$res = array();
		return $res;
	} //function

	
	/**
	 * Tagastame brauseri ja OS/i info stringina
	 */
	function getBrowserStr(){
		$browser = $this->browser;
		$os = $browser['OS']=='Win'?'WIN32':'LINUX';
		$br = $browser['BROWSER_AGENT'] == 'IE' ? 'IE' : 'MOZILLA';
		return $os.'-'.$br;
	} //function
	
	/**
	 * ühenduse/proksi parameetrite vektor
	 *
	 * Detail description
	 * @access    public
	 * @return    array
	 */
	function getConnect(){
		$ret=array();
		if(defined('DD_PROXY_HOST') && DD_PROXY_HOST) $ret['proxy_host'] = DD_PROXY_HOST;
		if(defined('DD_PROXY_PORT') && DD_PROXY_PORT) $ret['proxy_port'] = DD_PROXY_PORT;
		if(defined('DD_PROXY_USER') && DD_PROXY_USER) $ret['proxy_user'] = DD_PROXY_USER;
		if(defined('DD_PROXY_PASS') && DD_PROXY_PASS) $ret['proxy_pass'] = DD_PROXY_PASS;
		if(defined('DD_TIMEOUT') && DD_TIMEOUT) $ret['timeout'] = DD_TIMEOUT;
		return $ret;
	} // end func

	
	
} //class






###########################################################################
###########################################################################
###########################################################################
/**
 * DigiDoc XML faili parser
 *
 * Loeb DigiDoc faili komponendid. Teisendab ta failidega kujult failideta
 * kujule ja vastupidi.
 * @access       public
 * @package      DigiDoc
 * @todo         Lisada kõik funktsioonid, mis on seotud ddoc konteineriga
 * ja xml-i töötlusega selles koos failide lisamise eemaldamise 
 * funktsioonidega
 */
class Parser_DigiDoc{

	/**
	 * DigiDoc XML faili hoidja
	 * @var       string
	 * @access    private
	 */
	var $xml;

	/**
	 * Parsitava faili formaat
	 * Description
	 * @var       array
	 * @access    private
	 */
	var $format;


	/**
	 * Parsitava faili versioon
	 * Description
	 * @var       array
	 * @access    private
	 */
	var $version;


	/**
	 * Description
	 * @var       array
	 * @access    private
	 */
	var $xmlarray;
	
	/**
	 * Kõik XML failist leitud datafailide tagid.
	 * @var       array
	 * @access    private
	 */
	var $dataFilesXML;
	
	/**
	 * Töökaust failide hoidmiseks
	 * @var       string
	 * @access    private
	 */
	var $_workPath;
	

	/**
	 * Constructor.
	 * @param      string  $xml       Parsitava DDoc faili XML sisu
	 */
	function Parser_DigiDoc($xml=''){
		$this->xml = $xml;
		$this->xmlarray = $xml?$this->Parse($this->xml):false;
		$this->setDigiDocFormatAndVersion();
		$this->workPath = DD_FILES."/";//.session_id().'/';
		if (!is_dir($this->workPath))
			if(File::DirMake($this->workPath) != DIR_ERR_OK)
				die('Error accessing workpath:'.$this->workPath);
	} // end func

	
	/**
	 * Teisendab XML-i array kujule
	 *
	 * @param     string     $xml
	 * @param     string     $XMLPart  Parsida kas 'body' või 'header' või ''
	 * @access    public
	 * @return    array
	 */
	function Parse($xml, $XMLPart=''){
		$us = new XML_Unserializer();
		$us->unserialize($xml, FALSE);

		$xml2 = $us->getUnserializedData();

		if (isset($xml2['SOAP-ENV:Body']))
		$body = $xml2['SOAP-ENV:Body'];
		else
			$body=$xml2;

		$body = @current($body);

		if (isset($body['SignedDocInfo']['format']))
			$this->format = $body['SignedDocInfo']['format'];

		if (isset($body['SignedDocInfo']['version']))
			$this->version = $body['SignedDocInfo']['version'];

		switch(strtolower($XMLPart)){
			case 'body':
				$xml2 = $body;
				break;
			case 'header':
				$xml2 = $xml2['SOAP-ENV:Header'];
				#$xml = current($xml);
				break;
		} //switch
		
		return $xml2;
	
	} // end func

	/**
	 * tagastab ddoc-is olevad andmefailid.
	 *
	 * Tagastab kõik digidoc failis olevad andmefailid arrayna.
	 * @param     string     $xml
	 * @access    public
	 * @return    array
	 */
	function getFilesInfo($xml){
		$fs = $this->_getFilesXML($xml);

		$us = new XML_Unserializer();

		$ret = array();
		foreach($fs as $key=>$val){
			$us->unserialize($val, FALSE);
			$ret[] = $us->getUnserializedData();
		} //foreach
		return $ret;
	} // end func


	/**
	 * Määrab digidoc-i failiformaadi ja versiooni XML põhjal.
	 *
	 * @param     string     $xml
	 * @access    public
	 * @return    array
	 */
	function setDigiDocFormatAndVersion($xml='') {
		if ($xml=='')
			$xml=$this->xml;
		if ($xml) {
			preg_match("'(\<SignedDoc.*\/SignedDoc\>)'Us", $xml, $match); 
			$content = $match[1];
			preg_match("'format=\"(.*)\"'Us", $content, $match);	$this->format = $match[1];
			preg_match("'version=\"(.*)\"'Us", $content, $match);	$this->version = $match[1];
		} else {
			$this->format = "DIGIDOC-XML";
			$this->version = "1.3";
		}
	}
	

	/**
	 * Tagastab digidoc-s sisalduvad allkirjad
	 *
	 * Tagastab digidoc-s olevad allkirjad arrayna.
	 * @param     string     $xml
	 * @access    public
	 * @return    array
	 */
	function getSignaturesInfo( $xml ){
		$fs = $this->_getSignsXML( $xml );
		$us = new XML_Unserializer();
		$ret = array();
		foreach($fs as $key=>$val){
			$us->unserialize($val, FALSE);
			$ret[] = $us->getUnserializedData();
		} //foreach
		return $ret;
	} // end func

	
	
	/**
	 * Short description.
	 *
	 * Detail description
	 * @param     boolean    $withLocalFiles
	 * @access    public
	 * @return    string
	 */
	function getDigiDoc( $withLocalFiles = FALSE ){
		$files = $this->_getFilesXML($this->xml);
		$nXML = $this->xml;
		$func = $withLocalFiles ? 'file2hash' : 'hash2file';
	
		while(list(,$file) = each($files)){
			$nXML = str_replace($file, $this->$func($file), $nXML);
		} //while
		#echo "<hr>getDigiDoc: funk: $func <pre>".htmlentities($nXML).'</pre><hr>';

		return $nXML;
	} // end func

	
	function getDataFileBlockHashCoded($xml)
	{   
                //digidoc-xml 1.1 korral ei arvuta hashcode kuju
                //2010.04, ahto. digidoc 1.0, 1.1 ja 1.2 puhul ei arvuta räsi, hoiame täiskujul
                if (
                        $this->format == 'SK-XML' ||
                        ($this->format == 'DIGIDOC-XML' && ($this->version == '1.1' || $this->version == '1.2'))
                    )
                    return $xml;

				//KNOWN_BUGS #1: Kui siin antakse sisse datafail, kus failinimes on täpitähti, siis ta
				//tagastab teises formaadis selle täpitähe. Ja edasi lähevad asjad katki, kui 
				//getXMLDataFileElementCanonized() allpool uuesti kutsutakse. Idee: xml parser ei peaks xml-i 
				//kirjutama faili, äkki läheb sellega katki. Või siis on parseris viga täpitähtedega ja tuleks
				//parserit muuta.
				//
                //leiame <DataFile> tag-i kanoniseeritud kuju, ContentType = EMBEDDED_BASE64
                $xml_datafile_element_base64_canonized = $this->getXMLDataFileElementCanonized($xml, 'file');

                if ($xml_datafile_element_base64_canonized == "")
                    return $xml;
                
				$hash = base64_encode(pack("H*", sha1(str_replace("\r\n", "\n", $xml_datafile_element_base64_canonized)))); // Õige

                //leiame <DataFile> elemendi, ContentType = HASHCODE
                $xml_datafile_element_hashcode_canonized =
                    $this->getXMLDataFileElementCanonized($xml_datafile_element_base64_canonized, 'filesha1', $hash);

                if ($xml_datafile_element_hashcode_canonized == "")
                    return $xml;

                return $xml_datafile_element_hashcode_canonized;
	}
        
	
	/**
	 * Teisendab Datafaile tagi filega kujult hash-koodiga kujule.
	 *
	 * Teisendab DigiDoc failist saadud DataFile tagides oleva faili
	 * hash/koodi sisaldavale kujule ja salvestades saadud faili kohalikule
	 * kettale määratud kausta.
	 * @param     string     $xml
	 * @access    private
	 * @return    string
	 */
	function file2hash($xml){

		if(preg_match("'ContentType\=\"HASHCODE\"'s",$xml))
		{
			return $xml;
		} 
		else {
			preg_match("'Id=\"(.*)\"'Us", $xml, $match);	$Id = $match[1]; // Saame teada faili identifikaatori
			File::SaveLocalFile( $this->workPath.session_id().'_'.$Id, $xml); // salvestame algfaili
			
			//2009.11.27, Ahto, digidoc versioon 1.0 (formaat SK-XML) failide käsitlemiseks saadame nad dds-ile täiskujul,
			//mitte hashcode-ituna.
                        //2010.04, ahto. digidoc 1.0, 1.1 ja 1.2 puhul ei arvuta räsi, hoiame täiskujul
                        if (
                                $this->format == 'SK-XML' ||
                                ($this->format == 'DIGIDOC-XML' && ($this->version == '1.1' || $this->version == '1.2'))
                            )
			{
				return $xml;
			}
			else
			{
				//2009.09.07, Ahto, parandatud üle <DataFile> elemendi hashcode kuju leidmine
				return $this->getDataFileBlockHashCoded($xml);
			}
		} //else
	} // end func
	
	
	/**
	 * Asendab Datafile tagides hash-koodid vastavate failidega
	 *
	 * Asendab antud XML-s hash-koodiga XML-i faili sisaldavaks XML tagiks
	 * @param     string     $xml
	 * @access    private
	 * @return    string
	 */
	function hash2file($xml){
		if( preg_match("'ContentType\=\"HASHCODE\"'s", $xml) ){
			 preg_match("'Id=\"(.*)\"'Us", $xml, $match);		$Id = $match[1];
			 $nXML = File::readLocalFile($this->workPath.session_id().'_'.$Id);			 
			return $nXML;
		} else {
			return $xml;
		} //else
	} // end func
	
	
	/**
	 * Tagastab faili kohta HASH koodi.
	 *
	 * Genereerib failile vajaliku XML tagi ja leiab selle HASH-koodi. 
	 * Saadud faili XML salvestatakse vastavasse sessioonikausta.
	 * @param     array      $file          üleslaetud faili array
	 * @param     string     $Id            Faili ID DigiDoc-s
	 * @access    public
	 * @return    array
	 */
	function getFileHash($file, $Id='D0'){
            
		//2009.08.18, Ahto, html kodeerime failinime
		$xml = sprintf($this->getXMLtemplate('file'), htmlspecialchars($file['name']), $Id, $file['MIME'], $file['size'], chunk_split(base64_encode($file['content']), 64, "\n") );
		$sh = base64_encode(pack("H*", sha1( str_replace("\r\n","\n",$xml))));
		File::SaveLocalFile($this->workPath.session_id().'_'.$Id, $xml);
		//File::SaveLocalFile($this->workPath.session_id().'_'."test1.xml", $xml);
		$ret['Filename'] = $file['name'];
		$ret['MimeType'] = $file['MIME'];
		$ret['ContentType'] = 'HASHCODE';
		$ret['Size'] = $file['size'];
		$ret['DigestType'] = 'sha1';
		$ret['DigestValue'] = $sh;
		return $ret;
	} // end func
	
	/**
	 * Tagastab kõik andmefaili konteinerid antud XML failist.
	 *
	 * @param     string      $xml          Parsitav XML
	 * @access    private
	 * @return    array
	 */
	function _getFilesXML($xml){

		$x = array();
		$a = $b = -1;

		while(($a=strpos($xml, '<DataFile', $a+1))!==FALSE && ($b=strpos($xml, '/DataFile>', $b+1))!==FALSE){
			$x[] = preg_replace("'/DataFile>.*$'s", "/DataFile>", substr($xml, $a, $b));
		} //while

		if(!count($x)){
			$a = $b = -1;
			while(($a=strpos($xml, '<DataFileInfo', $a+1))!==FALSE && ($b=strpos($xml, '/DataFileInfo>', $b+1))!==FALSE){
				$x[] = preg_replace("'/DataFileInfo>.*$'s", "/DataFileInfo>", substr($xml, $a, $b));
			} //while
		}
		return $x;
	} // end func


	/**
	 * Tagastab kõik signatuuride konteinerid antud XML failist.
	 *
	 * @param     string      $xml          Parsitav XML
	 * @access    private
	 * @return    array
	 */
	function _getSignsXML($xml){
		if( preg_match_all("'(\<Signature.*\/Signature\>)'Us", $xml, $ret) ){
			return $ret[1];
		} elseif( preg_match_all("'(\<SignatureInfo.*\/SignatureInfo\>)'Us", $xml, $ret) ) {
			return $ret[1];
		} else {
			return array();
		} //else
	} // end func


	/**
	 * XML templiidid erinevatele päringutele
	 *
	 * @param     string     $type          Päritava XML-templiidi tüüp
	 * @access    private
	 * @return    string
	 */
        //2010.03, ahto, see meetod on kasutusel ainult juhtudeks, kui tuleb nullist täitsa uus DataFile element luua
	function getXMLtemplate($type){
		
		switch($type){

		case 'file':
				#File::VarDump('VER:'.$_SESSION['ddoc_version']);
			return '<DataFile'.($this->version == '1.3'?' xmlns="http://www.sk.ee/DigiDoc/v1.3.0#"':'').' ContentType="EMBEDDED_BASE64" Filename="%s" Id="%s" MimeType="%s" Size="%s"'.($this->format == 'SK-XML'?' DigestType="sha1" DigestValue="%s"':'').'>%s</DataFile>';
	    		break;
	    	case 'filesha1':
				#File::VarDump($_SESSION['ddoc_version']);
	    		return '<DataFile'.($this->version=='1.3'?' xmlns="http://www.sk.ee/DigiDoc/v1.3.0#"':'').' ContentType="HASHCODE" Filename="%s" Id="%s" MimeType="%s" Size="%s" DigestType="sha1" DigestValue="%s"></DataFile>';
	    		break;
	    	default:
	    		
	    } //switch
	} // end func

        //2010.03, ahto
        //
        //Tagastab vea korral tühiarray, muidu array atribuutide ja vastavate väärtustega
        function getXMLElementAttributes($xml_element)
        {
            require_once 'xml_parser.php';
            
			$tmp_f_name = tempnam(getcwd() . "/data/", 'w');
			file_put_contents($tmp_f_name, $xml_element);

            $error=XMLParseFile($parser, $tmp_f_name, 1);

			/*
			if ($error)
			{
				echo "<p>DigiDoc.class.php, getXMLElementAttributes(), xml parser error: $error";
				exit;
			}
			*/

            unlink($tmp_f_name);
            
            if(strlen($error))
                return Array();
            else
			{
                return $parser->structure["0"]["Attributes"];
			}
        }
        
        
        //$xml - <DataFile> element, ContentType = "EMBEDDED_BASE64", kas kanoniseerimata või kanoniseeritud
        //$type - kas 'file' või 'filesha1' ehk kas tagastame "ContentType = "EMBEDDED_BASE64" või "HASHCODE"
        //Tagastab <DataFile> elemendi, stringina, või tühistringi, vea korral
        function getXMLDataFileElementCanonized($xml, $type, $hash = "")
        {
                //datafaili sisu, base64 kujul
                if ($type == 'file')
                    $content = strip_tags($xml);
                else
                    $content = "";
		
                //leiame <DataFile> tag'i atribuutide array
                $attributes = $this->getXMLElementAttributes($xml);  
				
                if ($attributes == Array())
                    return "";
        
				//eemaldame alguses mõned atribuudid array'st - sellised, mida ei lisata
                //kanoniseerimise korral tähestiku järjekorras
                $xmlns = $attributes["xmlns"];
                unset($attributes["xmlns"]);
                
                //ContentType
                switch($type){
                    case 'file':
                            $attributes["ContentType"] = "EMBEDDED_BASE64";
                            break;
                    case 'filesha1':
                            $attributes["ContentType"] = "HASHCODE";
                            break;
                    default:	
                }
                
                //atribuudid tähestiku järjekorda
                ksort($attributes);
                
                //kõigepealt xmlns="..."
                $attributes_str = 'xmlns="' . $xmlns . '"';
                
                //edasi tähestiku järjekorras atribuudid ...
                foreach ($attributes as $attr => $value)
                {
                    $attributes_str .= " $attr=" . '"' . $value . '"';
                }
                
                //lõpuks DigestType="..." ja DigestValue="...", kui leiame HASHCODE kuju
                if ($type == 'filesha1')
                {
                    $attributes_str .= ' DigestType="sha1"';
                    
                    $attributes_str .= " DigestValue=" . '"' . $hash . '"';
                }
                
                return '<DataFile ' . $attributes_str . '>' . $content . '</DataFile>';
	} // end func


} // end class



###########################################################################
###########################################################################
###########################################################################
/**
 * File::DirMake Status: OK
 */
DEFINE ("DIR_ERR_OK",0);

/**
 * File::DirMake Status:	Path exists but not as directory
 */
DEFINE ("DIR_ERR_NOTDIR",1);

/**
 * File::DirMake Status:	Syntax error in path
 */
DEFINE ("DIR_ERR_SYNTAX",2);

/**
 * File::DirMake Status:	"mkdir" error with no parent
 */
DEFINE ("DIR_ERR_EMKDIR_1",3);

/**
 * File::DirMake Status:	"mkdir" error and parent exists
 */
DEFINE ("DIR_ERR_EMKDIR_2",4);

/**
 * File::DirMake Status:	"mkdir" error after creating parent
 */
DEFINE ("DIR_ERR_EMKDIR_3",5);

/**
 * Failide funktsioonid
 *
 * Klass sisaldab kõiki failidega seotud funktsioone, nagu üleslaadimine, 
 * salvestamine, nimede genereerimine, kaustade loomine.
 *
 * @package      DigiDoc
 */
class File{
	
		
	/**
	 * constructor
	 */
	function File(){
	    return true;
	} // end func
	
	/**
	 * Kaustade/alamkaustade loomiseks
	 *
	 * Loob kausta etteantud kohta, vajadusel ka kogu kaustapuu, kui 
	 * on õigused olemas selleks!
	 * @param     string	$strPath	Kausta nimi
	 * @access    public
	 * @return    integer	Tegevuse staatus
	 */
	function DirMake($strPath){
			   // If path exists nothing else can be done
		if ( file_exists($strPath) )
		   return is_dir($strPath) ? DIR_ERR_OK : DIR_ERR_NOTDIR;
			   // Backwards references are not allowed
		if (ereg("\.\.",$strPath) != 0) return DIR_ERR_SYNTAX;
			   // If it can create the directory that's all. If not then either path
			   // contains several dirs or error such as "permission denied" happened
		if (@mkdir($strPath)) return DIR_ERR_OK;
			   // Gets the parent path. If none then there was a severe error
		$nPos = strrpos($strPath,"/");
		if (!($nPos > 0)) return DIR_ERR_EMKDIR_1;
		$strParent = substr($strPath,0,$nPos);
			   // If parent exists then there was a severe error
		if (file_exists($strParent)) return DIR_ERR_EMKDIR_2;
			   // If it can make the parent
		$nRet = File::DirMake($strParent);
		if ($nRet == DIR_ERR_OK)
		   return mkdir($strPath) ? DIR_ERR_OK : DIR_ERR_EMKDIR_3;
		return $nRet;
	}

	
	/**
	 * Saadab antud faili brauserisse salvestamiseks
	 *
	 * Saadab etteantud faili brauserile salvestamiseks. Sunnib alati 
	 * brauserit avama salvestamise akent, sõltumata saadetava faili
	 * MIME-tüübist.
	 * @param     string      $name      Salvestatava faili nimi
	 * @param     mixed       $content   Salvestatava faili sisu
	 * @param     string      $MIME      Salvestatava faili MIME tüüp
	 * @param     string      $charset   Kasutatav koodileht. Vaikimis Western
	 * @access    public
	 * @return    boolean
	 */
	function saveAs($name, $content, $MIME = 'text/plain', $charset = ''){

		$name = str_replace("\'", "'", $name);	//2009.11.18, Ahto, asendame "\'" -> "'"
		ob_end_clean();
		ob_clean();
		$browser = File::getBrowser();
		if ($browser['BROWSER_AGENT'] == 'IE') {		
			$susisevad = array("š","ž","Š","Ž");
			$eisusise = array("sh","zh","Sh","Zh");
			$name = str_replace($susisevad, $eisusise,$name);
			$name = mb_convert_encoding($name, 'ISO-8859-1','UTF-8');
		}

		if($charset){
			header( 'Content-Type:' . $MIME . '; charset='.$charset );
		} else {
			header( 'Content-Type:' . $MIME );
		} //else
		header( 'Expires:' . gmdate('D, d M Y H:i:s') . ' GMT' ); #Alati aegunud, et ei loetaks cache-st
		$browser = File::getBrowser();
#		File::VarDump($browser);
		// IE need specific headers
		if ($browser['BROWSER_AGENT'] == 'IE') {		
			header('Cache-Control:must-revalidate, post-check=0, pre-check=0');
			header('Pragma:public');
		} else {

			header('Pragma:no-cache');
		}
			header('Content-Disposition:attachment; filename="'.$name.'"');
			Header("Content-Disposition-type: attachment"); 
		    Header("Content-Transfer-Encoding: binary");
//		echo utf8_decode( $content );
		echo $content;
		exit;

	} // end func

	
	/**
	 * Leiab kasutaja brauseri ja Op.sļæ½steemi
	 *
	 * Tagastab vektorina info kasutaja Op.süsteemi ja brauseri kohta
	 * - OS             : Operatsiooni süsteem (Win,Mac,Linux,Unix,OS/2,Other)
	 * - BROWSER_AGENT  : Kasutatav brauser
	 * - BROWSER_VER    : Brauseri versioon
	 * @access    public
	 * @return    array
	 */
	function getBrowser(){
		if (!empty($_SERVER['HTTP_USER_AGENT'])) {
			$HTTP_USER_AGENT = $_SERVER['HTTP_USER_AGENT'];
		} else if (!isset($HTTP_USER_AGENT)) {
			$HTTP_USER_AGENT = '';
		}
		$res=array();

		// 1. Platform
		if (strstr($HTTP_USER_AGENT, 'Win')) {
			$res['OS'] = 'Win';
		} else if (strstr($HTTP_USER_AGENT, 'Mac')) {
			$res['OS'] = 'Mac';
		} else if (strstr($HTTP_USER_AGENT, 'Linux')) {
			$res['OS'] = 'Linux';
		} else if (strstr($HTTP_USER_AGENT, 'Unix')) {
			$res['OS'] = 'Unix';
		} else if (strstr($HTTP_USER_AGENT, 'OS/2')) {
			$res['OS'] = 'OS/2';
		} else {
			$res['OS'] = 'Other';
		}

		// 2. browser and version
		if (preg_match('@Opera(/| )([0-9].[0-9]{1,2})@', $HTTP_USER_AGENT, $log_version)) {
			$res['BROWSER_VER'] = $log_version[2];
			$res['BROWSER_AGENT'] = 'OPERA';
		} else if (preg_match('@MSIE ([0-9].[0-9]{1,2})@', $HTTP_USER_AGENT, $log_version)) {
			$res['BROWSER_VER'] = $log_version[1];
			$res['BROWSER_AGENT'] = 'IE';
		} else if (preg_match('@OmniWeb/([0-9].[0-9]{1,2})@', $HTTP_USER_AGENT, $log_version)) {
			$res['BROWSER_VER'] = $log_version[1];
			$res['BROWSER_AGENT'] = 'OMNIWEB';
		//} else if (ereg('Konqueror/([0-9].[0-9]{1,2})', $HTTP_USER_AGENT, $log_version)) {
		// Konqueror 2.2.2 says Konqueror/2.2.2
		// Konqueror 3.0.3 says Konqueror/3
		} else if (preg_match('@(Konqueror/)(.*)(;)@', $HTTP_USER_AGENT, $log_version)) {
			$res['BROWSER_VER'] = $log_version[2];
			$res['BROWSER_AGENT'] = 'KONQUEROR';
		} else if (preg_match('@Mozilla/([0-9].[0-9]{1,2})@', $HTTP_USER_AGENT, $log_version)
				   && preg_match('@Safari/([0-9]*)@', $HTTP_USER_AGENT, $log_version2)) {
			$res['BROWSER_VER'] = $log_version[1] . '.' . $log_version2[1];
			$res['BROWSER_AGENT'] = 'SAFARI';
		} else if (preg_match('@Mozilla/([0-9].[0-9]{1,2})@', $HTTP_USER_AGENT, $log_version)) {
			$res['BROWSER_VER'] = $log_version[1];
			$res['BROWSER_AGENT'] = 'MOZILLA';
		} else {
			$res['BROWSER_VER'] = 0;
			$res['BROWSER_AGENT'] = 'OTHER';
		}
		return $res;
	} //function

	
	
	/**
	 * ajutise faili nimi
	 *
	 * tagastab genereeritud ajutise faili nime.
	 * @param     string     $ext      Faili laiend
	 * @access    public
	 * @return    string               Faili nimi
	 */
	function tempFile(){
	    return date('Ymd_His').'$'.substr('000'.rand(0,999), -3).'.'.$ext;
	} // end func

	
	/**
	 * loeb kohalikult ketalt faili sisu
	 *
	 * Loeb kohalikus arvutis oleva faili sisu ja tagastab selle.
	 * @param     string     $name     Faili nimi, mida lugeda
	 * @access    public
	 * @return    mixed
	 */
	function readLocalFile($name){
		$name = File::FixEstFileName($name);
		if(is_readable($name)){
			$content = file_get_contents($name);
			return $content;
		} else {
			return FALSE;
		} //else
	} // end func

	
	/**
	 * Salvestab lokaalseks failiks
	 *
	 * Salvestab antud sisu antud nimega faili, kui ei õnnestu 
	 * tagastatakse FALSE.
	 * @param     string     $name     Failinimi
	 * @param     string     $content  Faili sisu
	 * @access    public
	 * @return    mixed
	 */
	function saveLocalFile($name, $content){
		$name = File::FixEstFileName($name);

		if(touch($name)){
			$fh = fopen($name, 'wb');
			fwrite($fh, $content);
			fclose($fh);
			return TRUE;
		} else {
			return FALSE;
		} //else
	} // end func

	
	/**
	 * Tagastab etteantud nimega väljalt üleslaetud faili
	 *
	 * Tagastab faili, mis saadeti parameetris näidatud nimega formi 
	 * väljalt.
	 * @param     string     $name     Formi välja nimi, millega fail saadeti
	 * @access    public
	 * @return    array
	 */
	function getUploadedFile($name){
		if(isset($_FILES[$name])){
			$ret = array();
			$ret['type'] = $name;

			if (!is_dir(DD_UPLOAD_DIR))
				File::DirMake(DD_UPLOAD_DIR);

//				if(File::DirMake(DD_UPLOAD_DIR) != DIR_ERR_OK)

			$_FILES[$name]['name'] = str_replace("\'", "'", $_FILES[$name]['name']);	//2009.11.18, Ahto, asendame "\'" -> "'"

			if( move_uploaded_file($_FILES[$name]['tmp_name'], DD_UPLOAD_DIR.$_FILES[$name]['name']) ){
					$ret['name'] = $_FILES[$name]['name'];
					$ret['size'] = $_FILES[$name]['size'];
					$ret['MIME'] = $_FILES[$name]['type']!=""?$_FILES[$name]['type']:" ";
					$ret['error'] = $_FILES[$name]['error'];
					$ret['content'] = File::readLocalFile( DD_UPLOAD_DIR.$_FILES[$name]['name'] );
					unlink(DD_UPLOAD_DIR.$_FILES[$name]['name']);
			} else {
				$ret['error'] = '999: Cannot move uploaded file !!!';
			} //else
			return $ret;
		} else {
			return FALSE;
		} //else
	    
	} // end func

	
	/**
	 * Short description.
	 *
	 * Detail description
	 * @param     
	 * @since     1.0
	 * @access    private
	 * @return    void
	 * @throws    
	 */
	function FixEstFileName($name){
		//ļæ½ļæ½ļæ½ļæ½ ļæ½ļæ½ļæ½ļæ½

		$nameX = $name;
		#preg_match("'(.*)([^/\\\\]*)(\.\w+)$'U", $name, $match);
		#$nameX = $match[1].base64_encode($match[2]).$match[3];

		$name = preg_replace("'[^a-z0-9]'", "X", utf8_decode($name));
	    /*$name = str_replace('ļæ½','#otilde;', $name);
	    $name = str_replace('ļæ½','#auml;', $name);
	    $name = str_replace('ļæ½','#ouml;', $name);
	    $name = str_replace('ļæ½','#uuml;', $name);
	    $name = str_replace('ļæ½','#zacut;', $name);
	    $name = str_replace('ļæ½','#sacut;', $name);

	    $name = str_replace('ļæ½','#Otilde;', $name);
	    $name = str_replace('ļæ½','#Auml;', $name);
	    $name = str_replace('ļæ½','#Ouml;', $name);
	    $name = str_replace('ļæ½','#Uuml;', $name);
	    $name = str_replace('ļæ½','#Zacut;', $name);
	    $name = str_replace('ļæ½','#Sacut;', $name);
		if($name==$nameX){
			$name = utf8_decode($name);
			$name = str_replace('ļæ½','#otilde;', $name);
			$name = str_replace('ļæ½','#auml;', $name);
			$name = str_replace('ļæ½','#ouml;', $name);
			$name = str_replace('ļæ½','#uuml;', $name);

			$name = str_replace('ļæ½','#Otilde;', $name);
			$name = str_replace('ļæ½','#Auml;', $name);
			$name = str_replace('ļæ½','#Ouml;', $name);
			$name = str_replace('ļæ½','#Uuml;', $name);
		}
		*/
		return $nameX;
	} // end func

	/**
	 * Abifunktsioon debug-info väljastamiseks
	 * @param      mixed    $var     Muutuja mille väärtus väljastatakse
	 * @access     public
	 */
	function VarDump( $var ){
		#echo '<pre>';print_r($var);echo '</pre>';
		echo '
<pre>
=================================================================
';
		print_r($var);
		echo '
=================================================================
</pre>
';
	} //function


} // end class
?>