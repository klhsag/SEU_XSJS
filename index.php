<script>
var flag='start';
</script>

<?php
session_start();
require 'db_init.php';
if($GLOBALS['resFlag']!='ok'){
    echo $GLOBALS['errorMsg'];
    exit();
}

echo "<br>============debug==============<br>";

$now=date("Y-m-d H:i:s");
$stamp="2018-08-03 09:30:10";
echo "[",(strtotime($now)-strtotime($stamp)),"]";

echo "<br>==============================<br>";


if(isset($_SESSION['user']) && !empty($_SESSION['user'])){
    //echo "Welcome ",$_SESSION['user'],"<br>";
    echo "<script>flag='already_login';</script>";
}else{
    echo "<script>flag='not_login';</script>";
}

require './view/index.html';

?>
