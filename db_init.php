<?php
require 'database_keys/testdb0802.php';
$GLOBALS['resFlag']='start';
$GLOBALS['errorMsg']='none';
try {
    $conn=new PDO("mysql:host=$servername;dbname=$database",$db_username,$db_password,
        array(PDO::MYSQL_ATTR_INIT_COMMAND => "set names utf8"));
    
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    //===================================================
    //查member表,没有则创建
    
    $stmt=$conn->prepare("SELECT table_name FROM information_schema.tables
                                            WHERE table_name='member'");
    $stmt->execute();
    $resCnt=$stmt->rowCount();
    
    if($resCnt==0){
        //未创建member表
        $stmt=$conn->prepare("CREATE TABLE member (
                                                    username VARCHAR(10) NOT NULL,
                                                    password VARCHAR(10) NOT NULL,
                                                    score INT NOT NULL DEFAULT -1,
                                                    last_req_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                                    department_id VARCHAR(3) NOT NULL,
                                                    PRIMARY KEY(username)
                                                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        $stmt->execute();
        $stmt=$conn->prepare("INSERT INTO member (username, password, department_id)
                                                    VALUES (:u,:p,:d)");
        $username='';
        $password='';
        $department='';
        $stmt->bindParam(':u', $username);
        $stmt->bindParam(':p', $password);
        $stmt->bindParam(':d', $department);
        $testusers=array(
            array('213170000','09017000','09'),
            array('213170001','08017001','08'),
            array('213170002','08017002','08'),
            array('213170003','08017003','08'),
            array('213170004','07017004','07'),
            array('213170005','09017005','09'),
            array('213170006','09017006','09'),
            array('213170007','07017007','07'),
            array('213170008','07017008','07'),
            array('213170009','07017009','07'),
            array('213170010','07017010','07'),
            array('213170011','05017020','05'),
            array('213170012','09017030','09'),
            array('213170013','06017040','06'),
            array('213170014','05017050','05'),
            array('213170015','05017060','05'),
            array('213170016','05017070','05')
        );
        
        $mem_arr=$testusers;
        for($x=0;$x<count($mem_arr);$x++){
            $username=$mem_arr[$x][0];
            $password=$mem_arr[$x][1];
            $department=$mem_arr[$x][2];
            $stmt->execute();
        }
    }
    
    //=================================================
    //查qdb_choice表,没有则创建
    $stmt=$conn->prepare("SELECT table_name FROM information_schema.tables
                                            WHERE table_name='qdb_choice'");
    $stmt->execute();
    $resCnt=$stmt->rowCount();
    if($resCnt==0){
        $stmt=$conn->prepare("CREATE TABLE qdb_choice(
	                                               body VARCHAR(200) NOT NULL,
	                                               choice_A VARCHAR(50) NOT NULL,
	                                               choice_B VARCHAR(50) NOT NULL,
	                                               choice_C VARCHAR(50) NOT NULL,
	                                               choice_D VARCHAR(50) NOT NULL,
	                                               answer VARCHAR(1) NOT NULL
                                                   ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        $stmt->execute();
        
        $stmt=$conn->prepare("INSERT INTO qdb_choice (body, choice_A, choice_B,choice_C,choice_D,answer)
                                                    VALUES (:body,:ca,:cb,:cc,:cd,:ans);");
        $body='';
        $cha='';
        $chb='';
        $chc='';
        $chd='';
        $ans='';
        $stmt->bindParam(':body', $body);
        $stmt->bindParam(':ca', $cha);
        $stmt->bindParam(':cb', $chb);
        $stmt->bindParam(':cc', $chc);
        $stmt->bindParam(':cd', $chd);
        $stmt->bindParam(':ans', $ans);
        
        $testqch=array(
            array('1+1=?','1','2','11','不会','B'),
            array('东南大学在哪?','福建','河南','不知道','江苏','D'),
            array('我们的组名叫什么?','祖名还没想好','组长没想好','组名还没想好','没想好','C'),
            array('下面哪个人可以被称作黑客?','计算机技术高超的人','长得很黑的人','穿黑衣服的人','姓黑名客的人','D'),
            array('教室里25张桌子,20把椅子,请问老师几岁?','20+25=45岁','25-20=5岁','神经病','不知道','C')
        );
        
        $qch_arr=$testqch;
        for($x=0;$x<count($qch_arr);$x++){
            $body=$qch_arr[$x][0];
            $cha=$qch_arr[$x][1];
            $chb=$qch_arr[$x][2];
            $chc=$qch_arr[$x][3];
            $chd=$qch_arr[$x][4];
            $ans=$qch_arr[$x][5];
            $stmt->execute();
        }
    }
    
    //=================================================
    //查qdb_judge表,没有则创建
    $stmt=$conn->prepare("SELECT table_name FROM information_schema.tables
                                            WHERE table_name='qdb_judge';");
    $stmt->execute();
    $resCnt=$stmt->rowCount();
    if($resCnt==0){
        $stmt=$conn->prepare("CREATE TABLE qdb_judge(
	                                               body VARCHAR(200) NOT NULL,
	                                               answer VARCHAR(1) NOT NULL
                                                   ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        $stmt->execute();
        
        $stmt=$conn->prepare("INSERT INTO qdb_judge (body,answer)
                                                    VALUES (:body,:ans);");
        $body='';
        $ans='';
        $stmt->bindParam(':body', $body);
        $stmt->bindParam(':ans', $ans);
        
        $testqjd=array(
            array('1+1=2','T'),
            array('东南大学在福建','F'),
            array('我们的组名还没想好','F'),
            array('黑客长得很黑','F')
        );
        
        $qjd_arr=$testqjd;
        
        for($x=0;$x<count($qjd_arr);$x++){
            $body=$qjd_arr[$x][0];
            $ans=$qjd_arr[$x][1];
            $stmt->execute();
        }
    }
    
    $GLOBALS['resFlag']='ok';
}
catch(PDOException $ex){
    $GLOBALS['resFlag']='fail';
    $GLOBALS['errorMsg']=$ex->getMessage();
}

$conn=null;
?>