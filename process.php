<?php
session_start();

require_once("header.php");
require_once("lib.php");
require_once("token.php");

$out = "";
$csv = "";
$bat = "";
$sh = "";
?>

  <!-- Primary Page Layout
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
  <div style="padding:10px;">
   
   
        <h4>Page and Group Photo Exporter</h4>
       
       
       
<?php

$id = $_GET['id'];

$url = $base_url . "$id/albums?access_token=$token";

$response = file_get_contents ($url);

$json = json_decode($response);


$albums = $json -> data;
$more_albums = true;

$out = "<table border=1>";
$out .= "<tr><td>Album</td><td>Image</td><Td>Caption</td><td>Tagged</td><td>Date</td></tr>";
$csv = "\"Album\",\"Caption\",\"Tagged\",\"Date\",\"Filename\"\n";


while ($more_albums)
{
	foreach($albums as $album)
	{		
		$album_id = $album -> id;
		$dirname = str_file_filter($album -> name);
		
		$append = "mkdir \"$dirname\"\n";
		$append .= "cd \"$dirname\"\n";
		$sh .= $append;
		$bat .= $append;

		
		
		
		$photo_url = $base_url . $album_id . "/photos?fields=name,images,name_tags,created_time,updated_time&limit=500&access_token=$token";
		
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
				$csv .= "\"" . str_replace("\"", "\"\"", $album -> name)  . "\",";
				
				$images = $photo -> images;
				$min_width = 9999;
				$min_width_url = "";
				$max_width = 0;
				$max_width_url = "";
				foreach ($images as $image)
				{
					if ($image -> width < $min_width)
					{
						$min_width = $image -> width;
						$min_width_url = $image -> source;
					}
					if ($image -> width > $max_width)
					{
						$max_width = $image -> width;
						$max_width_url = $image -> source;	
					}
				}
				$out .= "<img src='$min_width_url' style='max-width: 200px;'>";
				
				$out .= "</td><td>";
				$out .= $photo -> name;
				$out .= "</td>";
				
				$csv .= "\"" . str_replace("\"", "\"\"", $photo -> name) . "\",";
				
				
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
				
				$csv .= "\"" . str_replace("\"", "\"\"", $str_tags) . "\",";
				
				$photodate = substr($photo -> created_time, 0, 10);
				$out .= "<td>" . $photodate . "</td>";
				
				$csv .= "\"" . str_replace("\"", "\"\"", $photodate) . "\",";
				
				$filename = $max_width_url;
				$filename = basename($max_width_url);
				if (strpos($filename, "?") != FALSE)
					$filename = substr($filename, 0, strpos($filename, "?"));
				
				$append = "wget --no-check-certificate \"$max_width_url\"\n";
				$sh .= $append;
				$bat .= $append;
				
				if (strpos( $max_width_url, "?") != FALSE)
				{
	
					$bat .= "rename \"" . str_replace("?", "@", basename($max_width_url)) . "\" \"$filename\"\n";
					$sh .= "mv \"" . basename($max_width_url) . "\" \"$filename\"\n";
				}
				
				$csv .= "\"" . str_replace("\"", "\"\"", $dirname ."/" .$filename) . "\"\n";
				
				
				$out .= "</tr>";	
				
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
	
	if (isset($json -> paging -> next))
	{
		$url = $json -> paging  -> next;
		$response = file_get_contents ($url);	
		$json = json_decode($response);
		$albums = $json -> data;
	}
	else
		$more_albums = false;
	
}

$out .= "</table>";

$csv_file = $album_id . "_" . time() . ".csv";
$sh_file = $album_id . "_" . time() . ".sh";
$bat_file = $album_id . "_" . time() . ".bat";

$sh .= "wget http://www.thinkingprojects.org/photo_exporter/csv/" . $csv_file;
$bat .= "wget http://www.thinkingprojects.org/photo_exporter/csv/" . $csv_file;


$handle = fopen("./csv/$csv_file", "w");
fwrite($handle,$csv);
fclose($handle);

$handle = fopen("./csv/$sh_file", "w");
fwrite($handle,$sh);
fclose($handle);

$handle = fopen("./csv/$bat_file", "w");
fwrite($handle,$bat);
fclose($handle);



?>
<a href="csv/<?php echo $csv_file;?>"><input type='button' value='Download CSV Metadata file'></a> 
<a href="mac.php?file=<?php echo $sh_file;?>"><input type='button' value='Download Images for Mac Script'></a> 
<a href="win.php?file=<?php echo $bat_file;?>"><input type='button' value='Download Images for Windows Script'></a>
<br />
<a href="main.php"><input type='button' value='Select another page or group' /></a>
<?php echo $out; ?>
       
       
        </p>
</div>
</div>

<?php require_once("footer.php"); ?>