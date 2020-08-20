
// Initialize Firebase
firebase.initializeApp(appConf);

// Retrieve Firebase Messaging object.
const messaging = firebase.messaging();

messaging.requestPermission().then(function() {
	maybeGetRegToken();
}).catch(function(err) {
	console.log('Unable to get permission to notify.', err);
});

messaging.onMessage(function(payload){
	switch(payload.data){
		case 'read':
			for(var i=0;i<unreadMessages.length;i++){
				unreadMessages[i].deliverRead();
			}
		break;
		case 'message':
			if(jQuery('.XD__body').length){
				jQuery('.XD__body').append(new Message({text: payload.data.message,date: payload.data.date,target:'in'}));
			}
		break;
	}
	//display the message here
});
// Get Instance ID token. Initially this makes a network call, once retrieved
// subsequent calls to getToken will return from cache.

function maybeGetRegToken() {
	if(!wyzCheckCookie())
		getRegToken();
}

function getRegToken() {
	messaging.getToken().then(function(currentToken) {
		if (currentToken) {
			wyzUpdateUserToken(currentToken);
			//sendTokenToServer(currentToken);
			//updateUIForPushEnabled(currentToken);
		} else {
			// Show permission request.
			console.log('No Instance ID token available. Request permission to generate one.');
			// Show permission UI.
			//updateUIForPushPermissionRequired();
			setTokenSentToServer(false);
		}
	}).catch(function(err) {
		console.log('An error occurred while retrieving token. ', err);
		showToken('Error retrieving Instance ID token. ', err);
		setTokenSentToServer(false);
	});
}

function wyzSetCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+ d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function wyzGetCookie() {
    var key = "FCM_token=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for(var i = 0; i <ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(key) == 0) {
            return c.substring(key.length, c.length);
        }
    }
    return "";
}

function wyzCheckCookie() {
    var token = wyzGetCookie();
    return token != "";
}

function wyzUpdateUserToken(token) {

	jQuery.ajax({
        type: "POST",
        url: ajaxurl,
        data: 'action=update_user_token&nonce='+ajaxnonce+'&uid=' + fcm_user_ + '&token=' + token,
        success: function(response) {
			wyzSetCookie('FCM_token',token,14);
        },
        error: function (responce) {
	        toastr.error(responce.data);
	    }
    });
}