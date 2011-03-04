ID-AJAX
=======

AJAX põhine ID kaardi ja Mobiil-ID autentimine/allkirjastamine. Kui sk.ee lehel olevad näited on terviklahendused, siis see projekt
üritab kõik tegevused lammutada eraldi AJAX põhisteks käsklusteks. Nii on autentimist ja allkirjastamist mugav lisada suvalistele
veebiaplikatsioonidele.

Installeerimine
---------------

Kopeeri ID-AJAX oma HTTPS serveri juurkataloogi

    cd /path/to/htdocs
    git clone https://github.com/andris9/ID-AJAX.git

Muuda saidi juurkataloogis `.htaccess` faili, lisades reeglid URL'ide ümberkirjutamiseks - edaspidi hakkavad kõik päringud ID-AJAX mooduli pihta olema aadressidel */auth/tegevuseNimi*.

    RewriteEngine On
    RewriteBase /
    RewriteRule ^auth/cardAuthRequest$ ID-AJAX/IDCardAuth/cardauth.php?action=cardAuthRequest&%{QUERY_STRING} [NC]
    RewriteRule ^auth/(.*)$ ID-AJAX/auth.php?action=$1&%{QUERY_STRING} [NC]
    
Ava aadress `https://sinuserver.com/ID-AJAX/test/` ning proovi järgi, kas asi töötab.

Märkused
--------

  * Algselt autenditakse mitte *live-*, vaid testserveri pihta (vaata faili *ID-AJAX/conf.php*)
  * **NB!**Testserveri kasutamiseks tuleb eelnevalt oma Mobiil või ID kaardi sertifikaadid seal registreerida - seda saab teha aadressil [openxades.org](http://openxades.org/upload_cert.php)
  * Teenuse sertide asukoht on *ID-AJAX/lib/service_certs.pem*
  * DigiDoc teek puhverdab WSDL klassi faili *ID-AJAX/wsdl.class.php* - juhul kui *conf.php* faili muudetakse, tuleb see WSDL ära kustutada
  * Allkirjastatavate failidega majandab klass `FileStore` (*ID-AJAX/filestore.php*), mis vaikimisi salvestab kõigi failide andmed kausta */ID-AJAX/tmpfiles*. Juhul kui tekib soov need failid kuhugi mujale paigutada (MySQL vms), siis võib FileStore klassi üle kirjutada, oluline on vaid jätta selle klassi API samaks, sisu võib aga suvaline olla.