<?php
include_once("../eslip.php");
?>

<style>

html{
	font-family: verdana,helvetica,arial,sans-serif;
	font-size: 13px;
}

#openid_identifier{
	background: url(img/openid-mini.png) center left no-repeat;
	padding-left: 32px;
	height: 28px;
	font-family: verdana,helvetica,arial,sans-serif;
	font-size: 13px;
	margin: 5px 0px 5px 0px;
	border: 1px solid #999;
	width: 100%;
}

.button{
	height: 28px;
	border: 1px solid #999;
	margin: 5px 0px 5px 0px;
	font-family: verdana,helvetica,arial,sans-serif;
	font-size: 13px;
	display: table-cell;
}

#error {
       font-family: verdana,helvetica,arial,sans-serif;
       font-size:13px;
       border: 1px solid;
       margin: 10px 0px;
       padding:15px 10px 15px 50px;
       background-repeat: no-repeat;
       background-position: 10px center;
       color: #D8000C;
       background-color: #FFBABA;
       background-image: url(img/error.png);
}

#inner {
    display: table;
    width: 100%;
}
label {
    display: table-cell;
    width: 25%;
}
span {
    display: table-cell;
    width: 100%;
    padding: 0px 10px;
}

#wrap { margin:0 auto 0 auto; width:400px; }

</style>

<div id="wrap">
	<form name="openid_form" action='<?php echo $_GET["return_url"]; ?>' onsubmit="return validateForm()" method='POST'>
		<div id="parent">
		    <div id="inner">
		        <label>OpenID URL:</label> 
		        <span><input id="openid_identifier" type='text' name='openid_identifier' /></span>
		        <button name="open_id_login" class="button">Login</button>
		    </div>
		</div>
	</form>
	<div style="display:none" id="error">
	</div>
</div>

<script type="text/javascript">
    var emptyErrorMessage = '<?php echo emptyErrorMessage; ?>';
	function getParameter(d){var a,b,c=window.location.search.substring(1).split("&");for(a=0;a<c.length;a++)if(b=c[a].split("="),b[0]==d)return b[1];return null};
	function validateForm()
	{
		var openid_identifier = document.forms["openid_form"]["openid_identifier"].value;
		if (openid_identifier.trim() == '')
		{
			document.getElementById('error').innerHTML = emptyErrorMessage;
			document.getElementById('error').removeAttribute("style");
			return false;
		}
		else
		{
			return true;
		}
	}
	window.onload=function(){
		if (getParameter('error') !== null)
        {
        	document.getElementById('error').innerHTML = decodeURIComponent(getParameter('error'));
			document.getElementById('error').removeAttribute("style");
        }
	};
</script>