function executeSecurity(setbtn)
{
	if(setbtn === 'set1')
	{
		var paige = 'app_user_management_index';
		var title = 'User Management';
	}
	else if(setbtn === 'set2')
	{
		var paige = 'app_settings_index';
		var title = 'Security & Permissions';
		
	}
	else if(setbtn === 'set3')
	{
		var paige = 'app_application_management_index';
		var title = 'Application Management';
	}
	$('#modulename').html(title);
	$.post("./Controllers/" + paige + ".php", { },
	function(data) {
		$('#maincontent').html(data);
		sessionStorage.setItem("ModuleTitle", title);
		sessionStorage.setItem("navset", setbtn);
	});
}
