<!DOCTYPE html>
<?php
require 'config.php';
require 'execute-sql-functions.php';

?>
<head>
 <link rel="stylesheet" href="style.css"> 
<title>The Chubby Donuts</title>
<meta name="google-signin-scope" content="profile email">
<meta name="google-signin-client_id" content="822842326093-oo9m0j9se9020sqt97q0hf26rq3uqf37.apps.googleusercontent.com">
<script src="https://apis.google.com/js/platform.js" async defer></script>
</head>
<h1>The Chubby Donuts </h1>

<ul class="nav-menu">
<li><a href="home.php">Home</a></li>
<li><a href="user-profile.php">Visit User Profile</a></li>
<li><a href="#" onclick="signOut();">Sign out</a></li>
</ul>

<div class="clear-both"></div>
<script>
function signOut() {
	var auth2 = gapi.auth2.getAuthInstance();
    auth2.signOut().then(function () {
      console.log('User signed out.');
    });
    document.cookie = "user=; expires=Thu, 01 Jan 1970 00:00:00 UTC";
    document.cookie = "valid=; expires=Thu, 01 Jan 1970 00:00:00 UTC";
    document.cookie = "admin=; expires=Thu, 01 Jan 1970 00:00:00 UTC";
    document.cookie = "reloaded=; expires=Thu, 01 Jan 1970 00:00:00 UTC";
    document.cookie = "G_AUTHUSER_H=; expires=Thu, 01 Jan 1970 00:00:00 UTC";
    document.cookie = "G_ENABLED_IDPS=; expires=Thu, 01 Jan 1970 00:00:00 UTC";
	window.location.href = "http://www.ugrad.cs.ubc.ca/~n6o8/landing_page.php";
  }
</script>



