<?php

define ('SITE_ROOT', realpath(dirname(__FILE__)));
define ('MAX_PART', 8);

require_once SITE_ROOT . '/include/DbHandler.php';
require SITE_ROOT . '/libs/Slim/Slim.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

/**
 * Listing all files
 * method GET
 * url /files          
 */
$app->get('/files', function() use ($app) {
            $response = array();
            $db = new DbHandler();

            // fetching all user tasks
            $result = $db->getAllFiles();

            $response["error"] = false;
            $response["files"] = array();

            // looping through result and preparing tasks array
            while ($file = $result->fetch_assoc()) {
                $tmp = array();
                $tmp["file_id"] = $file["file_id"];
                $tmp["file_name"] = $file["file_name"];
                $tmp["file_path"] = $file["file_path"];
                $tmp["createdAt"] = $file["created_at"];
                array_push($response["files"], $tmp);
            }

            echoResponse(200, $response);
        });

/**
 * Create file
 * url - /file
 * method - POST
 * params - file_name, file_path
 */
$app->post('/file', function() use ($app) {
            // check for required params
            $response = array();
            
            $file_path = "/temp_files/";
            $file_path = $file_path . basename($_FILES['uploaded_file']['name']);
            // reading post params
            $filename = $_FILES['uploaded_file']['name'];
            $original_id = $app->request->post('original_id');
 
            if(move_uploaded_file($_FILES['uploaded_file']['tmp_name'], SITE_ROOT . $file_path)) {
                $db = new DbHandler();
                $res = $db->createFile($filename, $file_path, $original_id);

                if ($res == FILE_CREATED_SUCCESSFULLY) {
                    $count_temp_files_uploaded = $db->countTempFile($original_id);
                    if ($count_temp_files_uploaded == 8) {
                        $string = $filename;
                        $string = explode('.', $string);
                        array_pop($string);
                        $string = implode('.', $string);
                        merge_file($string, 8);
                        $db->deleteTempFile($original_id);
                        $db->createFileAfterMerge($string, '/uploads/' . $string);
                    }
                    $response["error"] = $count_temp_files_uploaded;
                    $response["message"] = "File uploaded successfully";
                    echoResponse(201, $response);
                } else if ($res == FILE_CREATE_FAILED) {
                    $response["error"] = true;
                    $response["message"] = "Oops! An error occurred while uploading";
                    echoResponse(200, $response);
                } else if ($res == FILE_ALREADY_EXISTED) {
                    $response["error"] = true;
                    $response["message"] = "Sorry, this file already existed";
                    echoResponse(200, $response);
                }
                
            }else {
                $response["error"] = true;
                $response["message"] = "Oops! An error occurred while uploading";
                echoResponse(200, $response);
            }
            
        });

/**
 * Echoing json response to client
 * @param String $status_code Http response code
 * @param Int $response Json response
 */
function echoResponse($status_code, $response) {
    $app = \Slim\Slim::getInstance();
    // Http response code
    $app->status($status_code);

    // setting response content type to json
    $app->contentType('application/json');

    echo json_encode($response);
}

/**
* Merge files have same original_id
*/
function merge_file($merged_file_name, $parts_num) {
        
    $content='';
    //put splited files content into content
    for($i=0;$i<$parts_num;$i++){
        $url = $_SERVER['DOCUMENT_ROOT'] . "/test_ftp/temp_files/" . $merged_file_name. ".00" .$i;
        $file_size = filesize($url);
        $handle    = fopen($url, 'rb') or die("error opening file");
        $content  .= fread($handle, $file_size) or die("error reading file");
        fclose($handle);
        unlink($url) or die("Couldn't delete file");
    }
        
    $url_after_merged = $_SERVER['DOCUMENT_ROOT'] . "/test_ftp/uploads/" . $merged_file_name;
    //write content to merged file
    $handle = fopen($url_after_merged, 'wb') or die("error creating/opening merged file");
    fwrite($handle, $content) or die("error writing to merged file");
    
}//end of function merge_file

$app->run();
?>