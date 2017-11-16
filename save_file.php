<?php 

require_once '../test_ftp/include/DbHandler.php';

$file_path = "images/";
$file_path = $file_path . basename($_FILES['uploaded_file']['name']);
if(move_uploaded_file($_FILES['uploaded_file']['tmp_name'],$file_path))	{
	$response = array();
	$db = new DbHandler();
    $res = $db->createFile(basename($_FILES['uploaded_file']['name']), $file_path);

    if ($res == FILE_CREATED_SUCCESSFULLY) {
        $response["error"] = false;
        $response["message"] = "File uploaded successfully";
        echo $response;
    } else if ($res == FILE_CREATE_FAILED) {
        $response["error"] = true;
        $response["message"] = "Oops! An error occurred while uploading";
        echo $response;
    } else if ($res == FILE_ALREADY_EXISTED) {
        $response["error"] = true;
        $response["message"] = "Sorry, this file already existed";
        echo $response;
    }
}else {
	$response["error"] = true;
    $response["message"] = "Oops! An error occurred while uploading";
    echo $response;
}
?>