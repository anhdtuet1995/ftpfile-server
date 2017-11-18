<?php

define ('SITE_ROOT', realpath(dirname(__FILE__)));

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
            
            $file_path = "/images/";
            $file_path = $file_path . basename($_FILES['uploaded_file']['name']);
            // reading post params
            $filename = $app->request->post('file_name');
 
            if(move_uploaded_file($_FILES['uploaded_file']['tmp_name'], SITE_ROOT . $file_path)) {
                $db = new DbHandler();
                $res = $db->createFile($filename, $file_path);

                if ($res == FILE_CREATED_SUCCESSFULLY) {
                    $response["error"] = false;
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

$app->run();
?>