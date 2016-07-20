<?php
session_start();

if (isset($_GET['token']))
{
	$_SESSION['token'] = $_GET['token'];	
}
require_once("header.php");
require_once("lib.php");
require_once("token.php");

?>

  <!-- Primary Page Layout
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
  <div class="container">
    <div class="row">
      <div class="one-half column" style="margin-top: 18%">
        <h4>Page and Group Photo Exporter</h4> 
        <p>
        Select which page or group's photos you would like to export:<br><br>
        <form method='get' action='process.php'>
        <select name='id'>
        
        <?php
		// get groups
		$url = $base_url . "me/groups?access_token=$token";
		
		echo "URL $url";
		
		$response = file_get_contents($url);
		$json = json_decode($response);
		
		$groups = $json -> data;
		$more_groups = true;
		while ($more_groups)
		{
			foreach ($groups as $group)
			{
				echo "<option value='" . $group -> id . "'>" . $group -> name . " [Group]</option>";	
			}
			
			if (isset($json -> paging -> next))
			{
				$url = $json -> paging  -> next;
				$response = file_get_contents ($url);	
				$json= json_decode($response);
				$groups = $json -> data;
			}
			else
				$more_groups = false;
		}
		
		// get pages
		$url = $base_url . "me/likes?access_token=$token";
		$response = file_get_contents($url);
		$json = json_decode($response);
		
		$groups = $json -> data;
		$more_groups = true;
		while ($more_groups)
		{
			foreach ($groups as $group)
			{
				echo "<option value='" . $group -> id . "'>" . $group -> name . " [Page]</option>\n";	
			}
			
			if (isset($json -> paging -> next))
			{
				$url = $json -> paging  -> next;
				$response = file_get_contents ($url);	
				$json= json_decode($response);
				$groups = $json -> data;
			}
			else
				$more_groups = false;
		}
		
		?>
        
        </select>
        
 
        <br><br>
        Clicking "go" will initaite the export.  It may take several minutes for the export to complete.<br />
        <input type="submit" value="Go">
        </form>
        </p>
      </div>
    </div>
  </div>

<?php require_once("footer.php"); ?>