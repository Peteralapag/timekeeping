<!DOCTYPE html>
<html>
<?php
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
$Main = new Main;
?>
<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>TIMEKEEPING - <?php echo $Main->GetMainConfig('company_name',$db)?></title>
<link rel="stylesheet" href="../Models/Styles/fa/css/all.css">
<link rel="stylesheet" href="../Models/Styles/bootstrap-5.0.2/bootstrap.min.css">
<link rel="stylesheet" href="../Models/Styles/dataTables.bootstrap.min.css">
<link rel="stylesheet" href="../Models/Styles/styles.css">
<link rel="stylesheet" href="../Libraries/loader/loader.css">
<link rel="stylesheet" href="../Models/Styles/jquery-ui.css">
<link rel="stylesheet" href="../Models/Styles/jquery.dataTables.min.css">
<script src="../Models/Scripts/jquery.min.js"></script>
<script src="../Models/Scripts/bootstrap-5.0.2/bootstrap.min.js"></script>
<script src="../Models/Scripts/sweetalert.min.js"></script>
</head>
<style>
/*.mainbody { background: url('Libraries/media/wallpaper/<?php echo $Main->GetUserWallpaper($_SESSION["application_username"],$db); ?>') no-repeat;background-size: cover;	background-position: center;	} */
</style>
<body id="mainbody" class="mainbody">
