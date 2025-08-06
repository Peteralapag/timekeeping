<?PHP
session_start();
session_destroy();
echo '
	<script>
		sessionStorage.clear();
		window.location.href="/";
	</script>
';