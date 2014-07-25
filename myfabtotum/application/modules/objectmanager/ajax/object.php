<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/myfabtotum/ajax/config.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/myfabtotum/ajax/lib/database.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/myfabtotum/ajax/lib/utilities.php';

/** SAVE POST PARAMETERS */
$_object_id = $_POST["id_object"];
$_printable = $_POST["printable"];


/** UTIL PARAMS */
$_printable_files[] = '.gc';
$_printable_files[] = '.gcode';
$_printable_files[] = '.nc';

/** LOAD DB */
$db = new Database();

/** LOAD OBJECE FROM DB */
$_object = $db->query("select * from sys_objects where id=".$_object_id);
$_object = $_object[0];

$_object['date_insert']  =  mysql_to_human($_object['date_insert']);
$_object['date_updated'] =  mysql_to_human($_object['date_updated']);

/** LOAD OBJECT'S FILES FROM DB */
$_object_files = $db->query("select * from sys_obj_files where id_obj=".$_object_id);


$_files = array();

foreach($_object_files as $_obj){
      
    $_temp = $db->query("select * from sys_files where id=".$_obj['id_file']);

    if(isset($_temp[0])){
            
        $_files[$_temp[0]['id']] = $_temp[0];
        
    }
}

$db->close();

/** RESPONSE */
echo minify(json_encode(array('object'=>$_object, 'files'=>array('number' => count($_files), 'data' => $_files))));


?>