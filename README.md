ID-AJAX
=======

AJAX põhine ID kaardi ja Mobiil-ID autentimine/allkirjastamine. Kui sk.ee lehel olevad näited on terviklahendused, siis see projekt
üritab kõik tegevused lammutada eraldi AJAX põhisteks käsklusteks. Nii on autentimist ja allkirjastamist mugav lisada suvalistele
veebiaplikatsioonidele. Zone III paketis ning Veebimajutus.ee Standard+ paketis peaks teek töötama ilma eriliselt seadistamata.

Installeerimine
---------------

Kopeeri ID-AJAX oma HTTPS serveri juurkataloogi

    cd /path/to/htdocs
    git clone https://github.com/andris9/ID-AJAX.git

või lae alla [ZIP arhiiv](https://github.com/andris9/ID-AJAX/zipball/master) ja paki lahti.

Muuda saidi juurkataloogis `.htaccess` faili, lisades reeglid URL'ide ümberkirjutamiseks - edaspidi hakkavad kõik päringud ID-AJAX mooduli pihta olema aadressidel */auth/tegevuseNimi*.

    RewriteEngine On
    RewriteBase /
    RewriteRule ^auth/cardAuthRequest$ ID-AJAX/IDCardAuth/cardauth.php?action=cardAuthRequest&%{QUERY_STRING} [NC]
    RewriteRule ^auth/(.*)$ ID-AJAX/auth.php?action=$1&%{QUERY_STRING} [NC]

ID kaardi tugi peab olema serveris olemas. See on automaatselt olemas näiteks Zone III paketis ja Veebimajutus.ee Standard+ paketis - nende pakettide puhul midagi täiendavalt ise ID kaardi töölepanekuks tegema ei pea.
    
Ava aadress `https://sinuserver.com/ID-AJAX/test/` ning proovi järgi, kas asi töötab.

Märkused
--------

  * Algselt autenditakse mitte *live-*, vaid testserveri pihta (vaata faili *ID-AJAX/conf.php*)
  * **NB!**Testserveri kasutamiseks tuleb eelnevalt oma Mobiil või ID kaardi sertifikaadid seal registreerida - seda saab teha aadressil [openxades.org](http://openxades.org/upload_cert.php)
  * Teenuse sertide asukoht on *ID-AJAX/lib/service_certs.pem*
  * DigiDoc teek puhverdab WSDL klassi faili *ID-AJAX/wsdl.class.php* - juhul kui *conf.php* faili muudetakse, tuleb see WSDL ära kustutada
  * Allkirjastatavate failidega majandab klass `FileStore` (*ID-AJAX/filestore.php*), mis vaikimisi salvestab kõigi failide andmed kausta */ID-AJAX/tmpfiles*. Juhul kui tekib soov need failid kuhugi mujale paigutada (MySQL vms), siis võib FileStore klassi üle kirjutada, oluline on vaid jätta selle klassi API samaks, sisu võib aga suvaline olla.
  
Live demo
---------

Toimivat demo võib näha järgmiselt aadressilt - [https://www.digituvastus.org/ID-AJAX/test/](https://www.digituvastus.org/ID-AJAX/test/). Juhul kui enda koopia korral on kõik korrektselt seadistatud peab lõpptulemus olema sama.

Litsents
--------

Ma ei suutnud tuvastada kas sk.ee poolt pakutavatel teekidel on mingi litsents või mitte. Igatahes kataloogis *ID-AJAX/static/* olevad .JAR failid ning osaliselt IDCardModule.js, samuti ka ID-AJAX/lib/include kataloogis olevad failid on (c) sk.ee, kõik muu on BSD.

Kasutamine
----------

Kõige parem on vaadata, kuidas töötab *ID-AJAX/test/index.html*. Kasutatud JavaScript sõltub Prototype teegist. */ID-AJAX/static/IDCardModule.js* on vajalik ID kaardiga allkirjastamiseks, */ID-AJAX/static/AUTH.js* sisaldab tegevusi Mobiil-ID'ga autentimiseks ja allkirjastamiseks ning ID kaardiga autentimiseks. *auth_sign.js* on näiteskript autentimise ja allkirjastamise läbiviimiseks.

Näiterakendus teeb loob iga allkirjastamise jaoks uue faili, aga kui viide faili juurde (fid väärtus) on olemas, saab sama faili ka korduvalt allkirjastada. Mõistlik oleks võibolla *auth/addFile* meetod üldse välja lülitada ja tekitada allkirjastatavad failid mingil muul moel, kui et kasutaja need ise üles laeb.

Allkirjastamise jaoks peab lehel olema DOM element, mille *id* väärtuseks on *pluginLocation*. Skriptis *auth_sign.js* lisab selle lehele automaatselt funktsioon *init_card_plugin()* seega ei pea element olema HTML'i vägisi sisse kirjutatud. 