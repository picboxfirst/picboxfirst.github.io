<?php
	session_start();

	$host = "localhost";
	$user = "root";
	$pass = "123p1cb0x";
	$dbse = "123picbox";

	$db_conn = mysql_connect($host, $user, $pass) or die("could not connect");
	mysql_select_db($dbse, $db_conn);
	
	require_once 'imageResizer.php';

	$url = "http://cdn3.123picbox.com/";
	// $url = "http://badrullami.github.io/";  

	$query = mysql_query("select * from images where is_downloaded=0 limit 1");
	
	while($row = mysql_fetch_array($query)){
		
		mysql_query("update images set is_downloaded=1 where id=".$row['id']);

		$title = beautify($row['title']);

		// original images
		$file_original = $row['image_id'].'-'.$title."-o.".pathinfo($row['Original'], PATHINFO_EXTENSION);
		$path_original = "assets/img/original/03/".$file_original;
		
		$path_large = "";
		$path_large2048 = "";

		// medium800
		$file_medium800 = $row['image_id'].'-'.$title."-vm.".pathinfo($row['Original'], PATHINFO_EXTENSION);
		$path_medium800 = "assets/img/medium800/03/".$file_medium800;

		// medium
		$file_medium = $row['image_id'].'-'.$title."-m.".pathinfo($row['Original'], PATHINFO_EXTENSION);
		$path_medium = "assets/img/medium/03/".$file_medium;

		// small
		$file_small = $row['image_id'].'-'.$title."-s.".pathinfo($row['Original'], PATHINFO_EXTENSION);
		$path_small = "assets/img/small/03/".$file_small;

		// thumbnail
		$file_thumbnail = $row['image_id'].'-'.$title."-t.".pathinfo($row['Original'], PATHINFO_EXTENSION);
		$path_thumbnail = "assets/img/thumbnail/03/".$file_thumbnail;

		try {
			
			if($row['Original_Width'] > $row['Original_Height']) :

				/* resize thumbnail 100 */
				$width_thumbnail = 100;
				$height_thumbnail = (100/$row['Original_Width'])*$row['Original_Height'];
				smart_resize_image(null , file_get_contents($row['Original']), $width_thumbnail , $height_thumbnail, false , $path_thumbnail , false , false ,100);

				/* resize small 240 */
				$width_small = 240;
				$height_small = (240/$row['Original_Width'])*$row['Original_Height'];
				smart_resize_image(null , file_get_contents($row['Original']), $width_small , $height_small, false , $path_small , false , false ,100);

				/* resize medium 500 */
				$width_medium = 500;
				$height_medium = (500/$row['Original_Width'])*$row['Original_Height'];
				smart_resize_image(null , file_get_contents($row['Original']), $width_medium , $height_medium, false , $path_medium , false , false ,100);

				/* resize medium 800 */
				$width_medium800 = 800;
				$height_medium800 = (800/$row['Original_Width'])*$row['Original_Height'];
				smart_resize_image(null , file_get_contents($row['Original']), $width_medium800 , $height_medium800, false , $path_medium800 , false , false ,100);

				$width_large=0; $height_large=0; $width_large2048=0; $height_large2048=0;

				if($row['Original_Width'] > 1024) : 

					// large
					$file_large = $row['image_id'].'-'.$title."-l.".pathinfo($row['Original'], PATHINFO_EXTENSION);
					$path_large = "assets/img/large/03/".$file_large;

					/* resize large 1024 */
					$width_large = 1024;
					$height_large = (1024/$row['Original_Width'])*$row['Original_Height'];
					smart_resize_image(null , file_get_contents($row['Original']), $width_large , $height_large, false , $path_large , false , false ,100);
				endif;

				if($row['Original_Width'] > 2048) : 
					// large2048
					$file_large2048 = $row['image_id'].'-'.$title."-vl.".pathinfo($row['Original'], PATHINFO_EXTENSION);
					$path_large2048 = "assets/img/large2048/03/".$file_large2048;

					/* resize large 2048 */
					$width_large2048 = 2048;
					$height_large2048 = (2048/$row['Original_Width'])*$row['Original_Height'];
					smart_resize_image(null , file_get_contents($row['Original']), $width_large2048 , $height_large2048, false , $path_large2048 , false , false ,100);
				endif;

				/* get original binary */
				// smart_resize_image(null , file_get_contents($row['Original']), $row['Original_Width'] , $row['Original_Height'], false , $path_original, false , false ,100);

				mysql_query("update images set 
					Thumbnail ='".$url.$path_thumbnail."', 
					Thumbnail_Width =".$width_thumbnail.", 
					Thumbnail_Height =".$height_thumbnail.",
					Small ='".$url.$path_small."', 
					Small_Width =".$width_small.", 
					Small_Height =".$height_small.",
					Medium ='".$url.$path_medium."', 
					Medium_Width =".$width_medium.", 
					Medium_Height =".$height_medium.",
					Medium_800 ='".$url.$path_medium800."', 
					Medium_800_Width =".$width_medium800.", 
					Medium_800_Height =".$height_medium800.",
					Large ='".$url.$path_large."', 
					Large_Width =".$width_large.", 
					Large_Height =".$height_large.",
					Large_2048 ='".$url.$path_large2048."', 
					Large_2048_Width =".$width_large2048.", 
					Large_2048_Height =".$height_large2048.",
					web_thumbnail ='".$url.$path_small."',
					web_single ='".$url.$path_medium800."',
					is_downloaded =1     
					where id=".$row['id']);
					echo "row id". $row['id'];
			else :
				/* resize thumbnail 100 */
				$height_thumbnail = 100;
				$width_thumbnail = (100/$row['Original_Height'])*$row['Original_Width'];
				smart_resize_image(null , file_get_contents($row['Original']), $width_thumbnail, $height_thumbnail, false , $path_thumbnail , false , false ,100);

				/* resize small 240 */
				$height_small = 240;
				$width_small = (240/$row['Original_Height'])*$row['Original_Width'];
				smart_resize_image(null , file_get_contents($row['Original']), $width_small, $height_small, false , $path_small , false , false ,100);

				/* resize medium 500 */
				$height_medium = 500;
				$width_medium = (500/$row['Original_Height'])*$row['Original_Width'];
				smart_resize_image(null , file_get_contents($row['Original']), $width_medium, $height_medium, false , $path_medium , false , false ,100);

				/* resize medium 800 */
				$height_medium800 = 800;
				$width_medium800 = (800/$row['Original_Height'])*$row['Original_Width'];
				smart_resize_image(null , file_get_contents($row['Original']),  $width_medium800, $height_medium800, false , $path_medium800 , false , false ,100);

				$height_large=0; $width_large=0; $height_large2048=0; $width_large2048=0;

				if($row['Original_Height'] > 1024) : 
					// large
					$file_large = $row['image_id'].'-'.$title."-l.".pathinfo($row['Original'], PATHINFO_EXTENSION);
					$path_large = "assets/img/large/03/".$file_large;

					/* resize large 1024 */
					$height_large = 1024;
					$width_large = (1024/$row['Original_Height'])*$row['Original_Width'];
					smart_resize_image(null , file_get_contents($row['Original']), $width_large, $height_large, false , $path_large , false , false ,100);
				endif;

				if($row['Original_Height'] > 2048) : 
					// large2048
					$file_large2048 = $row['image_id'].'-'.$title."-vl.".pathinfo($row['Original'], PATHINFO_EXTENSION);
					$path_large2048 = "assets/img/large2048/03/".$file_large2048;

					/* resize large 2048 */
					$height_large2048 = 2048;
					$width_large2048 = (2048/$row['Original_Height'])*$row['Original_Width'];
					smart_resize_image(null , file_get_contents($row['Original']), $width_large2048, $height_large2048, false , $path_large2048 , false , false ,100);
				endif;

				/* get original binary */
				// smart_resize_image(null , file_get_contents($row['Original']), $row['Original_Width'], $row['Original_Height'], false , $path_original, false , false ,100);

				mysql_query("update images set 
					Thumbnail ='".$url.$path_thumbnail."', 
					Thumbnail_Width =".$width_thumbnail.", 
					Thumbnail_Height =".$height_thumbnail.",
					Small ='".$url.$path_small."', 
					Small_Width =".$width_small.", 
					Small_Height =".$height_small.",
					Medium ='".$url.$path_medium."', 
					Medium_Width =".$width_medium.", 
					Medium_Height =".$height_medium.",
					Medium_800 ='".$url.$path_medium800."', 
					Medium_800_Width =".$width_medium800.", 
					Medium_800_Height =".$height_medium800.",
					Large ='".$url.$path_large."', 
					Large_Width =".$width_large.", 
					Large_Height =".$height_large.",
					Large_2048 ='".$url.$path_large2048."', 
					Large_2048_Width =".$width_large2048.", 
					Large_2048_Height =".$height_large2048.",
					web_thumbnail ='".$url.$path_small."',
					web_single ='".$url.$path_medium800."',
					is_downloaded =1     
					where id=".$row['id']);
			endif;
		}
		catch (Exception $e) {
		    echo 'Caught exception: ',  $e->getMessage(), "\n";
		    continue;
		}
	}
?>


<?php 
	// beautify
	function beautify($title)
	{
		$keyReplace = array(" ", "*", "\"", "#", ",", "&", ":", "(", ")", "?", "'", ".", "!", ";", "/", "~", "%", "$", "<", ">", "[", "]", "@", "#", "=", "{", "}", "|");
		$title = strtolower(str_replace($keyReplace, "-", $title));
		$title = str_replace(array("----", "---", "--"), "-", $title);
		return $title;
	}
?>
