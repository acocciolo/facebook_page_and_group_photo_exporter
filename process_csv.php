#!/usr/bin/php
<?php

require_once("lib.php");

$token = $argv[1];
$id = $argv[2];
$email = $argv[3];
$host = $argv[4];

$csv = "";
$bat = "";
$sh = "";


$url = $base_url . "$id/albums?access_token=$token";

$response = file_get_contents ($url);

$json = json_decode($response);


$albums = $json -> data;
$more_albums = true;

$csv = "\"Album\",\"Caption\",\"Tagged\",\"Date\",\"Place\",\"Address\",\"Latitude\",\"Longitude\",\"Filename\"\n";


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


		
		$photo_url = $base_url . $album_id . "/photos?fields=name,images,name_tags,created_time,updated_time,place&limit=500&access_token=$token";
		
		$photo_response = file_get_contents ($photo_url);
		$json_photo = json_decode($photo_response);
		$photos = $json_photo -> data;
		
		$more_photos = true;
		while ($more_photos)
		{
			foreach ($photos as $photo)
			{
			
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
				
			
				
				$csv .= "\"" . str_replace("\"", "\"\"", $str_tags) . "\",";
				
				$photodate = substr($photo -> created_time, 0, 10);
				
				
				$csv .= "\"" . str_replace("\"", "\"\"", $photodate) . "\",";
				
				
				$place = "";
				if (isset($photo -> place -> name))
					$place = $photo -> place -> name;
				$csv .= "\"" . str_replace("\"", "\"\"", $place) . "\",";
				
				
				$address = "";
				$lat = "";
				$long = "";
				if (isset($photo -> place -> location))
				{
					$location = $photo -> place -> location;
					if (isset($location -> street))
						$address = $location -> street . "\n";
					if (isset($location -> city))	
						$address .= $location -> city;
					if (isset($location -> state))	
						$address .= ", " . $location -> city;
					if (isset($location -> zip))	
						$address .= " " . $location -> zip;
					if (isset($location -> country))
						$address .= " " . $location -> country;		
						
					if (isset($location -> latitude))
						$lat = 	$location -> latitude;
					if (isset($location -> longitude))
						$long = $location -> longitude;
						
				}
				$csv .= "\"" . str_replace("\"", "\"\"", $address) . "\",";
				$csv .= "\"" . str_replace("\"", "\"\"", $lat) . "\",";
				$csv .= "\"" . str_replace("\"", "\"\"", $long) . "\",";
				

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


$csv_file = $id . "_" . time() . ".csv";
$sh_file = $id . "_" . time() . ".sh";
$bat_file = $id . "_" . time() . ".bat";

$sh .= "wget http://" . $host . "/photo_exporter/csv/" . $csv_file;
$bat .= "wget http://" . $host . "/photo_exporter/csv/" . $csv_file;


$handle = fopen("./csv/$csv_file", "w");
fwrite($handle,$csv);
fclose($handle);

$handle = fopen("./csv/$sh_file", "w");
fwrite($handle,$sh);
fclose($handle);

$handle = fopen("./csv/$bat_file", "w");
fwrite($handle,$bat);
fclose($handle);

$msg = "Photo export is complete.  You can downlaod your photo CSV metadata file at: http://$host/photo_exporter/csv/$csv_file";
$msg .= "\n\nIf you are using a Windows machine, this page will instruct you on how to download your photos: http://$host/photo_exporter/win.php?file=$bat_file";
$msg .= "\n\nIf you are using a Mac, this page will instruct you on how to download your photos: http://$host/photo_exporter/mac.php?file=$sh_file";
$msg .= "\n\nThanks for using this tool!";

$headers = 'From: photoexporter@thinkingprojects.org';
mail($email, "Photo export complete", $msg, $headers);
?>