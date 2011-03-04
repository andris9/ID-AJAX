AUTH = {
    
    api_url: "/auth/",
    
    language: "est",
    certificate:false,
    
    signatureHex: false,
    
    signatureRequest: false,
    signatureId:"S0",
    
    /***** AUTH *****/
    
    mobileAuthRequest: function(phone, options, callback){
        options = options || {};
        new Ajax.Request(this.api_url+"mobileAuthRequest",{
            method:"get",
            parameters:{
                phone: phone,
                message: options.message || "",
                lang: (options.lang || AUTH.language).toUpperCase(),
                t: +new Date()
            },
            onComplete: function(response){
                var data;

                // vigane staatus
                if(response.status!=200){
                    return callback && callback(new Error("Server responsed with code "+response.status), null);
                }

                // vigane JSON
                try{
                    data = response.responseText.evalJSON();
                }catch(E){
                    data = {status:"ERROR", "message":"Error parsing JSON"}
                }

                // Error
                if(data.status != "OK"){
                    return callback && callback(new Error(data.message || "Invalid request"), null);
                }

                return callback && callback(null, data);
            }
        });
    },

    mobileAuthStatus: function(sid, callback){
         new Ajax.Request(this.api_url+"mobileAuthStatus",{
            method:"get",
            parameters:{
                sid: sid,
                t: +new Date()
            },
            onComplete: function(response){
                var data;

                // vigane staatus
                if(response.status!=200){
                    return callback && callback(new Error("Server responsed with code "+response.status), null);
                }

                // vigane JSON
                try{
                    data = response.responseText.evalJSON();
                }catch(E){
                    data = {status:"ERROR", "message":"Error parsing JSON"}
                }

                // Waiting
                if(data.status == "WAITING"){
                    return window.setTimeout(function(){
                        AUTH.mobileAuthStatus(sid, callback);
                    }, 2000);
                }

                // OK
                if(data.status == "AUTHENTICATED"){
                    return callback && callback(null, data);
                }

                // ERROR
                return callback && callback(new Error(data.message || "Invalid request"));
            }
        });
    },

    cardAuthRequest: function(callback){
        new Ajax.Request(this.api_url+"cardAuthRequest",{
            method:"get",
            parameters:{
                t: +new Date()
            },
            onComplete: function(response){
                var data;

                // vigane staatus
                if(response.status!=200){
                    return callback && callback(new Error("Server responsed with code "+response.status), null);
                }

                // vigane JSON
                try{
                    data = response.responseText.evalJSON();
                }catch(E){
                    data = {status:"ERROR", "message":"Error parsing JSON"}
                }

                // OK
                if(data.status == "AUTHENTICATED"){
                    return callback && callback(null, data);
                }

                // ERROR
                return callback && callback(new Error(data.message || "Invalid request"));
            }
        });
    },
    
    /***** SIGN *****/
    
    initSigning: function(){
        try{
            IDCardModule.loadSigningPlugin(this.language);
        }catch(error){
            throw error;
            this.showError(error);
        }
    },
    
    getCert: function(){
        try {
            this.certificate = new IDCardModule.IdCardPluginHandler(this.language).getCertificate();
            return true;
        } catch(error) {
            this.showError(error)
            return false;                
        }
    },
    
    sign: function(hash){
        try {
            this.signatureHex = new IDCardModule.IdCardPluginHandler(this.language).sign(this.certificate.id, hash);
            return true;
        } catch(error) {
             this.showError(error);
             return false;
        }
    },
    
    onsigned: function(){
        alert(this.signatureHex);
    },
    
    preparesignatureRequest: function(fileId, callback){
        if(!AUTH.certificate && !(AUTH.getCert() && AUTH.certificate)){
            return callback && callback(new Error("Error retrieving certificate"))
        }
        new Ajax.Request(this.api_url+"cardPrepareSignature", {
            method:"post",
            parameters:{
                fileId: fileId,
                certId: this.certificate.id, 
                certHex: this.certificate.cert
            },
            onComplete: (function(response){
                // vigane staatus
                if(response.status!=200){
                    return callback && callback(new Error("Server responsed with code "+response.status), null);
                }
                        
                // vigane JSON
                try{
                    data = response.responseText.evalJSON();
                }catch(E){
                    data = {status:"ERROR", "message":"Error parsing JSON"}
                }
                        
                // Error
                if(data.status != "OK"){
                    return callback && callback(new Error(data.message || "Invalid request"), null);
                }
                
                if(data.signatureRequest){
                    this.signatureRequest = data.signatureRequest;
                }else{
                    return callback && callback(new Error("Invalid signature hash"), null);
                }
                
                this.signatureId = data.signatureId || "S0";
                
                this.finalizeSignature(callback);
            }).bind(this)
        })
    },
    
    finalizeSignature: function(callback){
        AUTH.sign(this.signatureRequest);
        if(!this.signatureHex){
            return callback && callback(new Error("Signing failed - no signature"));
        }
        new Ajax.Request(this.api_url+"cardFinalizeSignature", {
            method:"post",
            parameters:{
                signatureId: this.signatureId,
                signatureHex: this.signatureHex
            },
            onComplete: function(response){
                // vigane staatus
                if(response.status!=200){
                    return callback && callback(new Error("Server responsed with code "+response.status), null);
                }
                        
                // vigane JSON
                try{
                    data = response.responseText.evalJSON();
                }catch(E){
                    data = {status:"ERROR", "message":"Error parsing JSON"}
                }
                        
                // Error
                if(data.status != "SIGNED"){
                    return callback && callback(new Error(data.message || "Invalid request"), null);
                }
                
                callback && callback(null, data);
            }
        });
    },
    
    
    mobileSignRequest: function(fid, options, callback){
        options = options || {};
        new Ajax.Request(this.api_url+"mobileSignRequest",{
            method:"get",
            parameters:{
                fid: fid,
                message: options.message || "",
                lang: (options.lang || "EST").toUpperCase(),
                t: +new Date()
            },
            onComplete: function(response){
                var data;

                // vigane staatus
                if(response.status!=200){
                    return callback && callback(new Error("Server responsed with code "+response.status), null);
                }

                // vigane JSON
                try{
                    data = response.responseText.evalJSON();
                }catch(E){
                    data = {status:"ERROR", "message":"Error parsing JSON"}
                }

                // Error
                if(data.status != "OK"){
                    return callback && callback(new Error(data.message || "Invalid request"), null);
                }

                return callback && callback(null, data);
            }
        });
    },

    mobileSignStatus: function(sid, callback){
        new Ajax.Request(this.api_url+"mobileSignStatus",{
            method:"get",
            parameters:{
                sid: sid,
                t: +new Date()
            },
            onComplete: function(response){
                var data;

                // vigane staatus
                if(response.status!=200){
                    return callback && callback(new Error("Server responsed with code "+response.status), null);
                }

                // vigane JSON
                try{
                    data = response.responseText.evalJSON();
                }catch(E){
                    data = {status:"ERROR", "message":"Error parsing JSON"}
                }

                // Waiting
                if(data.status == "WAITING"){
                    return window.setTimeout(function(){
                        AUTH.mobileSignStatus(sid, callback);
                    }, 2000);
                }

                // OK
                if(data.status == "SIGNED"){
                    return callback && callback(null, data);
                }

                // ERROR
                return callback && callback(new Error(data.message || "Invalid request"));
            }
        });
    },
    
    showError: function(error){
        alert(error && error.message || error);
    }
}
