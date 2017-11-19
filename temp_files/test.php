<?php
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
        
    $url_after_merged = $_SERVER['DOCUMENT_ROOT'] . "/test_ftp/files/" . $merged_file_name;
    //write content to merged file
    $handle = fopen($url_after_merged, 'wb') or die("error creating/opening merged file");
    fwrite($handle, $content) or die("error writing to merged file");
    
}//end of function merge_file

$string = 'appvn.apk.000';
$string = explode('.', $string);
array_pop($string);
$string = implode('.', $string);
merge_file($string, 8);
?>