<?php
include 'util.php';

my_session_start();

if(isset($_SESSION["artist_profile_id"]) and isset($_SESSION["user_email_address"])){
	$artist_profile_id = $_SESSION["artist_profile_id"];
	//echo $artist_profile_id;
	$user_email_address = $_SESSION["user_email_address"];
	//echo $user_email_address;
}
if(isset($_FILES["file"]["type"]))
{
	$validextensions = array("jpeg", "jpg", "png");
	$temporary = explode(".", $_FILES["file"]["name"]);
	$file_extension = end($temporary);
	if ((($_FILES["file"]["type"] == "image/png") || ($_FILES["file"]["type"] == "image/jpg") || ($_FILES["file"]["type"] == "image/jpeg")) 
		&& ($_FILES["file"]["size"] < 4194304)//Approx. 100kb files can be uploaded.
		&& in_array($file_extension, $validextensions)) 
	{
		if ($_FILES["file"]["error"] > 0)
		{
			echo "Return Code: " . $_FILES["file"]["error"] . "<br/><br/>";
		}
		else
		{
			if (file_exists("upload/photo_upload_data/" . $_FILES["file"]["name"])) {
				echo $_FILES["file"]["name"] . " <span id='invalid'><b>already exists.</b></span> ";
			}
			else {
				$sourcePath = $_FILES['file']['tmp_name']; 
				$timestamp = time();// Storing source path of the file in a variable
                $targetPath = "upload/photo_upload_data/".$timestamp .$_FILES['file']['name']; // Target path where file is to be stored
				// echo("<script>console.log('Target: ".$targetPath."');</script>");
				// echo("<script>console.log('Source: ".$sourcePath."');</script>");
				$_SESSION["photo_file_path"] = $targetPath;

                if (move_uploaded_file($sourcePath, $targetPath)){ // Moving Uploaded file
                    echo "<span id='success'>Image Uploaded Successfully...!!</span><br/>";
                    include 'connection_open.php';

                    $query = "UPDATE artist_profile
                        SET artist_photo_path = '$targetPath' 
                        WHERE artist_profile_id='" . $_SESSION["artist_profile_id"] . "'";
                    $result = mysqli_query($dbc,$query)
                    or die('Error querying database.: ' . mysqli_error($dbc));
                    include 'connection_close.php';
                }else{
                    echo "<span id='invalid'>**Some problem occurred please try again later***<span>";
                }
			}
		}
	}
	else
	{
		echo "<span id='invalid'>***Invalid file Size or Type***<span>";
	}
}
?>