<?php
session_start();

require_once("header.php");
require_once("lib.php");
require_once("token.php");

$out = "";
$max_photo = 0;
?>

  <!-- Primary Page Layout
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
  <div style="padding:10px;">
   
   
        <a href='main.php'><input type='button' style='float: right' value='Select another page or group' /></a><h4>Page and Group Photo Exporter</h4>  
       
       
       
<?php

#get email

$url = $base_url . "me?fields=email&access_token=$token";
$response = file_get_contents ($url);
$json = json_decode($response);
if (isset($json -> email))
	$email = $json -> email;
else
{
	echo "Error - Unable to get email address";
	exit;
}



$id = $_GET['id'];

$url = $base_url . "$id/albums?access_token=$token";

$response = file_get_contents ($url);

$json = json_decode($response);


$albums = $json -> data;
$more_albums = true;

$out = "Success. You will be sent a CSV file via email in the next couple of minutes with your photo metadata as well as instrutions on downloading your photos.  The first 50 photos are displayed below:
<br><table border=1>";
$out .= "<tr><td>Album</td><td>Image</td><Td>Caption</td><td>Tagged</td><td>Date</td></tr>";
$cmd = "./process_csv.php $token $id $email " . $_SERVER['HTTP_HOST'] . " > /dev/null 2> /dev/null &";
shell_exec($cmd);


foreach($albums as $album)
{		
	if ($max_photo >= 50)
		break;
		
	$album_id = $album -> id;

	$photo_url = $base_url . $album_id . "/photos?fields=name,place,images,name_tags,created_time,updated_time&limit=50&access_token=$token";
	
	$photo_response = file_get_contents ($photo_url);
	
	
	$json_photo = json_decode($photo_response);
	$photos = $json_photo -> data;
	
	$more_photos = true;
	while ($more_photos)
	{
		foreach ($photos as $photo)
		{
			$out .= "<tr><td>";
			$out .= $album -> name ;
			$out .= "</td><td>";
		
			
			$images = $photo -> images;
			$min_width = 9999;
			$min_width_url = "";
			
			foreach ($images as $image)
			{
				if ($image -> width < $min_width)
				{
					$min_width = $image -> width;
					$min_width_url = $image -> source;
				}
			
			}
			$out .= "<img src='$min_width_url' style='max-width: 200px;'>";
			
			$out .= "</td><td>";
			$out .= $photo -> name;
			$out .= "</td>";
			
		
			
			
			$str_tags = "";
			$tag_cnt = 0;
			if (isset($photo -> name_tags))
			{
				$tags = $photo -> name_tags;
				foreach ($tags as $tag)
				{
					if ($str_tags == "")
						$str_tags = "Photo tags include: ";
					if ($tag_cnt > 0)
						$str_tags .= ", ";
					$str_tags .= $tag -> name;
					$tag_cnt++;	
				}
	
			}
			
			$out .= "<td>$str_tags</td>";
			
			
			
			$photodate = substr($photo -> created_time, 0, 10);
			$out .= "<td>" . $photodate . "</td>";
			
			
			/*
			$place = "";
			if (isset($photo -> place -> name))

				$place = $photo -> place -> name;
				
			$out .= "<td>$place</td>";
			*/
			
			$out .= "</tr>";	
			$max_photo ++;
			
			if ($max_photo == 50)
			{
				$more_photos = false;
				break;
			}
			
		}
		
		if (isset($json_photo -> paging -> next))
		{
			$url_photo = $json_photo -> paging  -> next;
			$photo_response = file_get_contents ($url_photo);	
			$json_photo = json_decode($photo_response);
			$photos = $json_photo -> data;
		}
		else
		{
			$more_photos = false;
			$sh .= "cd ..\n";
			$bat .= "cd ..\n";
		}
	}

}


$out .= "</table>";





?>


<?php echo $out;

#echo "<!--";
#print_r ( $json_photo) ;

#echo $cmd;

#echo "-->";
 
?>

       
        </p>
</div>
</div>

<?php require_once("footer.php"); ?>