<?php

class DbHandler {

	private $conn;
 
    function __construct() {
        require_once dirname(__FILE__) . './DbConnect.php';
        // opening db connection
        $db = new DbConnect();
        $this->conn = $db->connect();
    }

	/**
     * Creating new file
     * @param String $filename File file name
     */
    public function createFile($filename, $filepath, $original_id) {

    	$response = array();
 
        // First check if file already existed in db
        if (!$this->isFileExists($filename)) {
            // insert query
            $q = "INSERT INTO temp_files(temp_file_name, temp_file_url, original_id) values('$filename ', '$filepath', '$original_id')";
            $stmt = $this->conn->prepare($q);
            $result = $stmt->execute();
 
            $stmt->close();
 
            // Check for successful insertion
            if ($result) {
                // File successfully inserted
                return FILE_CREATED_SUCCESSFULLY;
            } else {
                // Failed to create file
                return FILE_CREATE_FAILED;
            }
        } else {
            // File with same file name already existed in the db
            return FILE_ALREADY_EXISTED;
        }
 
        return $response;

    }

/**
     * Creating new file
     * @param String $filename File file name
     */
    public function createFileAfterMerge($filename, $filepath) {

        $response = array();
 
        // First check if file already existed in db
        if (!$this->isFileAfterMergeExists($filename)) {
            // insert query
            $q = "INSERT INTO files(file_name, file_path) values('$filename ', '$filepath')";
            $stmt = $this->conn->prepare($q);
            $result = $stmt->execute();
 
            $stmt->close();
 
            // Check for successful insertion
            if ($result) {
                // File successfully inserted
                return FILE_CREATED_SUCCESSFULLY;
            } else {
                // Failed to create file
                return FILE_CREATE_FAILED;
            }
        } else {
            // File with same file name already existed in the db
            return FILE_ALREADY_EXISTED;
        }
 
        return $response;

    }

	/**
     * Checking for duplicate file by filename
     * @param String $filename filename to check in db
     * @return boolean
     */
    private function isFileExists($filename) {
        $stmt = $this->conn->prepare("SELECT temp_file_id from temp_files WHERE temp_file_name = ?");
        $stmt->bind_param("s", $filename);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }

    /**
     * Checking for duplicate file by filename
     * @param String $filename filename to check in db
     * @return boolean
     */
    private function isFileAfterMergeExists($filename) {
        $stmt = $this->conn->prepare("SELECT file_id from files WHERE file_name = ?");
        $stmt->bind_param("s", $filename);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }

    /**
     * Fetching all files
     */
    public function getAllFiles() {
        $stmt = $this->conn->prepare("SELECT * FROM files");
        $stmt->execute();
        $files = $stmt->get_result();
        $stmt->close();
        return $files;
    }

    /**
     * Count temp files with same origin_id
     */
    public function countTempFile($original_id) {
        $stmt = $this->conn->prepare("SELECT temp_file_id FROM temp_files where original_id = ?");
        $stmt->bind_param("s", $original_id);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows;
    }

    /**
     * Delete temp files with same origin_id
     */
    public function deleteTempFile($original_id) {
        $stmt = $this->conn->prepare("DELETE FROM temp_files where original_id = ?");
        $stmt->bind_param("s", $original_id);
        $stmt->execute();
        $stmt->close();
    }
}

?>