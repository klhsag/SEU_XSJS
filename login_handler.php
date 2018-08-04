<?php
session_start();

if(!isset($_POST['submit']) || empty($_POST['submit'])){
    echo "没有post";
}else if($_POST['submit']=="退出"){
    //echo "退出";
    $_SESSION['user']='';
}else{
    //这里要对$_POST["username"]和$_POST["password"]做验证,防止sql注入(还没写)
    require 'database_keys/testdb0802.php';
    
    try {
        $conn=new PDO("mysql:host=$servername;dbname=$database",$db_username,$db_password);
        
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $stmt=$conn->prepare("SELECT password FROM member WHERE username=:username");
        $username=$_POST["username"];
        $stmt->bindParam(':username', $username);
        
        $stmt->execute();
        
        $rows=$stmt->fetchAll();
        $rowCount=$stmt->rowCount();
        
        if($rowCount==1 && $rows[0][0]==$_POST["password"]){
            //echo "登录成功。注册时间：",$rows[0][1],"<br>";
            echo "ok";
            $_SESSION['user']=$username;
        }else{
            echo "fail";
        }
    }
    catch(PDOException $ex){
        echo "数据库连接失败<br>";
        echo $ex->getMessage();
    }
    
    $conn=null;
}

?>