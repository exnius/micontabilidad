
/*************************************
 *********** AUTH METHODS ************
 *************************************/

function OnLoadGApiCallback(){
    gapi.client.load('oauth2', 'v2', function() { });
    gapi.client.setApiKey(GOOGLE_API);
    var scopes = 'https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/userinfo.email';
    window.setTimeout(checkAuth,1);
}

function checkAuth() {
    var scopes = 'https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/userinfo.email';
    gapi.auth.authorize({client_id: GOOGLE_API, scope: scopes, immediate: true}, handleGAuthResult);
}

function handleGAuthResult(authResult){
    if (authResult && !authResult.error) {
        getGoogleUserInfo(authResult);
    }
    $('.js-google-login').each(function(){
        this.onclick = clickOnGoogleLogin;
    });
}

function clickOnGoogleLogin(event){
    if(!$("body").data("googleData")){
        var scopes = 'https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/userinfo.email';
        gapi.auth.authorize({client_id: GOOGLE_API, scope: scopes, immediate: false}, function(resp){getGoogleUserInfo(resp)});
    }
    loginByGoogle($("body").data("googleData"));
    return false;
}

function getGoogleUserInfo(authResult){
    var request = gapi.client.request({
        'path': '/oauth2/v1/userinfo',
        'params': {'access_token': authResult.access_token,
                    'token_type' : 'Bearer',
                    'expires_in' :3600}
    });
    request.execute(function(resp) {
        $("body").data("googleData", resp);
    });
}

function loginByGoogle(authResult){
    console.info($("body").data("googleData"));
    Contabilidad.getEndPoint({async : true, success: function(resp){
        document.location.href = Contabilidad.private_home;
    }}).connectByGoogle(authResult);
}
