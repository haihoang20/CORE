<?php require 'config.php' ?>

<html>

    <head>
        <meta name="google-signin-scope" content="profile email">
        <meta name="google-signin-client_id" content="822842326093-oo9m0j9se9020sqt97q0hf26rq3uqf37.apps.googleusercontent.com">
        <script src="https://apis.google.com/js/platform.js" async defer></script>
    </head>
    
    <body>
        <h1> Landing Page for the Core</h1>
            <div class="g-signin2" data-onsuccess="onSignIn" data-theme="dark"></div><br>
            
    </body>
</html>


<script>
    function onSignIn(googleUser) {
        // Useful data for your client-side scripts:
        var profile = googleUser.getBasicProfile();
        console.log("ID: " + profile.getId()); // Don't send this directly to your server!
        console.log("Name: " + profile.getName());
        console.log("Image URL: " + profile.getImageUrl());
        console.log("Email: " + profile.getEmail());
        var id_token = googleUser.getAuthResponse().id_token;
        console.log("ID Token: " + id_token);

        document.cookie="valid=yes";

        window.location.href = "http://www.ugrad.cs.ubc.ca/~n6o8/home.php#" + profile.getEmail();
    }



</script>
