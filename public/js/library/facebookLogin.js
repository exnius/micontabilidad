Contabilidad.facebookLogin = function()
{
    this.onLoadMethods = [];
    this.permissions = "publish_actions,email";

    /*
     * Load Facebook libraries
     */
    this.initialize = function(){
        var tthis = this;
        var appId = window.ID_FB_API;
        window.fbAsyncInit = function() {
            FB.init({appId: appId, status: true, cookie: true,
                     xfbml: true, oauth: true});
            for(var item = 0; item < tthis.onLoadMethods.length; item++){
                tthis.onLoadMethods[item]();
            }
        };
        (function() {
            var e = document.createElement('script');
            e.async = true;
            e.src = document.location.protocol +
            '//connect.facebook.net/en_US/all.js';
            document.getElementById('fb-root').appendChild(e);
        }());
    };

    /*
     * login/register by facebook
     * callback => a callback function where it will go after facebook response
     */
    this.connect = function()
    {
        var facebookInfo = {};
        var tthis = this;
        FB.login(function(response) {
            if (response.status == "connected") {
                if (response.authResponse) {
                    facebookInfo.token = response.authResponse.accessToken;
                    facebookInfo.uid = response.authResponse.userID;
                    FB.api({
                        method: 'fql.query',
                        query: 'select current_location, email, name, sex from user where uid=' + response.authResponse.userID
                    }, function(response){
                        response = response[0];
                        if(!response || response.error){//facebook error
                            //do nothing
                        } else {
                            facebookInfo.email = response.email;
                            facebookInfo.full_name = response.name;
                            facebookInfo.current_location = response.current_location;
                            facebookInfo.gender = response.sex;
                            Contabilidad.getEndPoint({async : true, success: function(resp){
                                document.location.href = Contabilidad.private_home;
                            }}).connectByFacebook(facebookInfo);
                        }
                    });
                } else {
                    //do nothing
                }
            } else {
                callback({result: "refused"});
            }
        }, {scope: tthis.permissions});
    };

    /*
     * return given property
     */
    this._getSocialShare = function(user, socialNetwork, prp){
        if (user[socialNetwork] && user[socialNetwork][prp]){
            return user[socialNetwork][prp];
        }
        return false;
    };

    /*
     * check if the given user already has linked his fb account
     */
    this.isFacebookUser = function(user)
    {
        var token = this._getSocialShare(user, "facebook", "token");
        var uid = this._getSocialShare(user, "facebook", "uid");
        if (token && uid) {
            return true;
        }
        return false;
    };
}

$(document).ready(function(){
    var facebookLogin = new Contabilidad.facebookLogin();
    facebookLogin.initialize();
    
    $("body").click(function(event){
        if($(event.target).hasClass("js-facebook-login")){
            facebookLogin.connect();
            return false;
        }
    });
});