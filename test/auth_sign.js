$(document).observe("dom:loaded", function(){

    // mobiil ID'ga autoriseerimise vorm
    $("mid_form").observe("submit", function(event){
        Event.stop(event);
        do_mobile_auth()
    });

    // id kaardiga autoriseerimise nupp
    $("cardauth").observe("click", function(event){
        Event.stop(event);
        do_card_auth();
    });
    
    // lisa allkirjastamise plugin lehele
    init_card_plugin();

    // id kaardiga allkirjastamise nupp
    $("cardsign").observe("click", function(event){
        Event.stop(event);
        do_card_signing();
    });
    
    // mobiil id'ga allkirjastamise nupp
    $("midsign").observe("click", function(event){
        Event.stop(event);
        do_mobile_signing();
    });

});


function do_card_auth(){
    $("tulemus").show();
    $("tulemus").innerHTML = "Oota...";
    $("nupp").disable();

    AUTH.cardAuthRequest(function(error, data){
        if(error){
            $("nupp").enable();
            $("tulemus").innerHTML = "Viga: <br />"+error.message;
            return;
        }
        $("tulemus").innerHTML = "Tulemus: "+Object.toJSON(data);
        $("nupp").enable();
    });
}

function do_mobile_auth(){
    var phone = $("phone").value;
    $("tulemus").show();
    $("tulemus").innerHTML = "Oota...";
    $("nupp").disable();

    AUTH.mobileAuthRequest(phone,{message:"Testsõnum!"}, function(error, data){
        if(error){
            $("nupp").enable();
                $("tulemus").innerHTML = "Viga: <br />"+error.message;
            return;
        }
        $("tulemus").innerHTML = "Kood: "+data.code;

        AUTH.mobileAuthStatus(data.sid, function(error, data){
            if(error){
                $("nupp").enable();
                $("tulemus").innerHTML = "Viga: <br />"+error.message;
                return;
            }
            $("tulemus").innerHTML = "Tulemus: "+Object.toJSON(data);
            $("nupp").enable();
        });
    });
}

function init_card_plugin(){
    var pluginElm = new Element("div");
    pluginElm.setStyle({
        position: "absolute",
        left: "-1000px",
        top: "-1000px"
    });
    pluginElm.id="pluginLocation";
    document.body.appendChild(pluginElm);
    AUTH.initSigning();   
}

function do_card_signing(){
    
    // kõigepealt on vaja lisada uus fail, mida allkirjastada
    new Ajax.Request(AUTH.api_url+"addFile",{
        method: 'get',
        parameters: {
            filename: "test.txt",
            contents: $("contents").value
        },
        onComplete: function(response){
            var data;

            // vigane staatus
            if(response.status!=200){
                return alert("Server responsed with code "+response.status);
            }

            // vigane JSON
            try{
                data = response.responseText.evalJSON();
            }catch(E){
                data = {status:"ERROR", "message":"Error parsing JSON"}
            }

            // Error
            if(data.status != "OK"){
                return alert(data.message || "Invalid request");
            }
            
            // alusta allkirjastamist
            AUTH.preparesignatureRequest(data.FID, function(error, data){
                if(error){
                    return alert(error.message);
                }
                alert("Signed successfully! ");
                window.location.href="/auth/getDDOC?fid="+data.FID;
            }); 
        }
    });
    
}
    
function do_mobile_signing(){
    // kõigepealt on vaja lisada uus fail, mida allkirjastada
    new Ajax.Request(AUTH.api_url+"addFile",{
        method: 'post',
        parameters: {
            filename: "test.txt",
            contents: $("contents").value
        },
        onComplete: function(response){
            var data;
            // vigane staatus
            if(response.status!=200){
                return alert("Server responsed with code "+response.status);
            }

            // vigane JSON
            try{
                data = response.responseText.evalJSON();
            }catch(E){
                data = {status:"ERROR", "message":"Error parsing JSON"}
            }

            // Error
            if(data.status != "OK"){
                return alert(data.message || "Invalid request");
            }
            
            // alusta allkirjastamist
            $("sign-tulemus").update("Oota...").show();
            AUTH.mobileSignRequest(data.FID,{message:"Testsõnum!"}, function(error, data){
                if(error){
                    return alert(error.message);
                }
                $("sign-tulemus").update("Kood: "+data.code);
                AUTH.mobileSignStatus(data.sid, function(error, data){
                    if(error){
                        return alert(error.message);
                    }
                    alert("Signed successfully! ");
                    window.location.href="/auth/getDDOC?fid="+data.FID;
                });
            });
        }
    });
}