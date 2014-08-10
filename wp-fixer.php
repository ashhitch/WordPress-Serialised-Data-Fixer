<?php
error_reporting(E_ALL);
    $path = $_SERVER['DOCUMENT_ROOT'];
    include_once $path . '/wp-config.php';
    include_once $path . '/wp-load.php';
?>
<html lang="en"><head>
    <meta charset="utf-8">
    <title>WordPress Serialised Data Fixer</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
  <body>
  <div id="jumbo" class="jumbotron">
  <div class="container">   
    <h1>
      WordPress Serlizied Data Fixer</h1>
  </div>
</div>
<div class="container">
    <form action="wp-fixer.php" method="post">

    <div class="form-group">
        <input value="go" name="go" type="hidden">
        <button type="submit" class="btn btn-primary">Fix Data</button>
    </div>
    </form>

<?php

if( isset($_POST['go']) && $_POST['go'] =='go') 
{
$column = 'meta_value';   // the column with the serialised data in it
$index_column = 'meta_id';// the 

$tables =array($table_prefix.'postmeta',$table_prefix.'options');
//print_r($tables);
$count =0;

foreach ($tables as $value) {

    $table = $value;

    if ($table == $table_prefix.'options') {
        $column = 'option_value';   
        $index_column = 'option_id';
    }
    // now let's get the data...

    $SQL = "SELECT * FROM ".$table;
    $result = $wpdb->get_results($SQL);

    //print_r($result);
    if (!$result) { echo( mysql_error()); }

    foreach ($result as $row) {
        $count++;
        $value_to_fix = $row->$column;
        $index = $row->$index_column;
      
       // echo ('changing id: '.$index.'<br/>');
        //echo ('before: '.$value_to_fix.'<br/>');
        $fixed_value = __recalcserializedlengths($value_to_fix);
        //echo ('after: '.$fixed_value.'<br/>');
        
        // now let's create the update query...
        $UPDATE_SQL = "UPDATE ".$table." SET ".$column." = '".mysql_real_escape_string($fixed_value)."' WHERE ".$index_column." = '".$index."'";
       // echo $UPDATE_SQL;
        $wpdb->query($UPDATE_SQL); 

    }

    echo '<div class="alert alert-success" role="alert"><p>Serialization in table: '.$table.' fixed</p></div>';

}
    
 
}

function __recalcserializedlengths($sObject) {
    $__ret =preg_replace('!s:(\d+):"(.*?)";!e', "'s:'.strlen('$2').':\"$2\";'", $sObject );
    // return unserialize($__ret);
    return $__ret;
}
?>
<br>
Created by <a href="http://www.ashleyhitchcock.co.uk/">Ashley Hitchcock</a>
</div>
</body>



 