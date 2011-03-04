<?php

/*
 * See klass tuleks üle kirjutada
 */

define('FILESTORE_DIRECTORY', dirname(__FILE__).'/tmpfiles/');

/****** FILESTORE ******/

class FileStore{

    public static function add($contents, $name=false, $mime=false){
        $name = $name?$name:"data.bin";
        $mime = $mime?$mime:Sign::mime_content_type($name);
        $fid = time().md5(microtime().$name.$mime);
        $fileData = array(
            "fileName" => $name,
            "mimeType" => $mime,
            "contents" => $contents,
            "createTime" => time(),
            "signatures" => array(),
            "owner" => array(
                "UserSurname" => Auth::$data["UserSurname"],
                "UserGivenname" => Auth::$data["UserGivenname"],
                "UserIDCode" => Auth::$data["UserIDCode"]
            )
        );
        file_put_contents(FILESTORE_DIRECTORY.$fid, serialize($fileData));
        return $fid;
    }

    public static function retrieve($fid){
        $fid = trim($fid);
        if(!$fid){
            return false;
        }
        if(preg_match("/[^a-fA-F0-9]/",$fid)){
            return false;
        }
        $file = @unserialize(@file_get_contents(FILESTORE_DIRECTORY.$fid));
        return $file;
    }

    public static function addSignature($fid, $signature){
        $fileData = self::retrieve($fid);
        if($fileData){
            $fileData["signatures"][] = array(
                "contents"=>$signature,
                "data" => Sign::parseSignature($signature)
                );
            file_put_contents(FILESTORE_DIRECTORY.$fid, serialize($fileData));
            return true;
        }
        return false;
    }

    private static function map_signatures($n){
        return($n["contents"]);
    }

    public static function generateDDOC($fid){
        $fileData = self::retrieve($fid);
        if($fileData){
            Sign::$files = array();
            Sign::addFile($fileData["contents"], $fileData["fileName"], $fileData["mimeType"]);
            return array(
                "signedContents" => Sign::generateDDOC(array_map("FileStore::map_signatures",$fileData["signatures"])),
                "fileName" => $fileData["fileName"],
                "mimeType" => $fileData["mimeType"]);
        }
        return false;
    }

    public static function downloadDDOC($fid){
        $ddoc = self::generateDDOC($fid);
        if(!$ddoc || !$ddoc["signedContents"]){
            header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
            header("Content-type: text/html; Charset=utf-8");
            echo "<!doctype html>
                    <head>
                        <meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\" />
                        <title>Error</title>
                        <style type=\"text/css\">body{font-family: Helvetica, Sans-serif;}</style>
                    </head>
                    <body>
                        <h1>Not Found</h1>
                        <p>Invalid or expired data</p>
                    </body>
                  </html>";
        }else{
            header("Cache-Control: public");
            header("Content-Description: File Transfer");
            header("Content-Disposition: attachment; filename=\"".$ddoc["fileName"].".ddoc\"");
            header("Content-Type: application/x-ddoc");
            header("Content-Transfer-Encoding: binary");
            echo $ddoc["signedContents"];
        }
    }
}