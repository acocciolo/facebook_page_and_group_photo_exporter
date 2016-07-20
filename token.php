<?
if (isset($_SESSION['token']))
	$token = $_SESSION['token'];
else
{
	echo "Facebook access token is not set.  <a href='/photo_exporter/'>Please login again</a>.</body>";
	exit;
}
?>
