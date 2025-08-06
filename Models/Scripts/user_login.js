function pushLogin()
{
	var mode = 'c10cc6b684e1417e8ffa924de1e58373';
	var usr = $('#uname').val();
	var psw = $('#upass').val();
	if(usr == '')
	{
		app_alert("Login Error","Invalid Username","warning","Ok","username","no");
		return false;
	}
	else if(psw == '')
	{
		app_alert("Login Error","Invalid Password","warning","Ok","password","no");
		return false;
	}
	$('#submitlogin').html('Loging In <i class="fa fa-spinner fa-spin"></i>');
	$('#submitlogin').attr('disabled', true);
 	setTimeout(function()
 	{
	 	$.post("Processes/login_process.php", { mode: mode, username: usr, password: psw },
		function(data) {
			$('.loginresults').html(data);
			$('#submitlogin').html('Login');
			$('#submitlogin').attr('disabled', false);
		});
	},2000);
}
$(function()
{
	$("#uname").keyup(function (e)
	{
		if (e.key === 'Enter' || e.keyCode === 13)
		{	  	
			document.getElementById('upass').focus();
		}
	});
	$("#upass").on('keydown keyup focus', function(e)
	{
		if (e.key === 'Enter' || e.keyCode === 13) {
			$('#submitlogin').trigger('click');
		}
		var charCode = e.which || e.keyCode;
		var charStr = String.fromCharCode(charCode);
		if (charStr.toUpperCase() === charStr && charStr.toLowerCase() !== charStr && !e.shiftKey)
		{
			$('.caps-warning').fadeIn();
		} else {
		$('.caps-warning').fadeOut;
		}
 
    });	

	$('#loginbody').on('keydown', function(e)
	{
		if (e.key === 'Escape' || e.keyCode === 27)
		{
			screenEffect('screen');
    	}
        if (e.key === 'Enter' || e.keyCode === 13)
        {
        	screenEffect('login');
        }
    });
	$('.loginBtn').click(function()
	{
		screenEffect('login');
	});
});
function screenEffect(params)
{
	if(params == 'login')
	{
		document.getElementById('uname').focus();
		$('.loginresults').empty();
		$('#lowgow').fadeOut('slow');
		$('.loginBtn').fadeOut();
		$('#frosted').addClass("frosted-glass").fadeIn('slow');
	}
	if(params == 'screen')
	{
		$('#frosted').addClass("frosted-glass").fadeOut();
		$('#lowgow').fadeIn();
		$('.loginBtn').fadeIn();
	}
}

