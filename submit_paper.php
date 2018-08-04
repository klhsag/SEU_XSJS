<?php
session_start();
/*
 * res结构:
 * res={'type':'xxxx','score':xxxx,'wrongs':[x1,x2,x3,...]}
 * */
require 'exam_conf.php';
$response=array();
if(isset($_SESSION['user']) && !empty($_SESSION['user'])){
    //已登录
    if(!isset($_SESSION['exam_key']) || empty($_SESSION['exam_key'])){
        $response['flag']='fail';
        $response['msg']='试卷提交失败!错误信息:该账户尚未申请考试,请刷新页面重新考试.';
        echo json_encode($response);
        exit();
    }
    
    $exam_user=$_SESSION['user'];
    require 'database_keys/testdb0802.php';
    try {
        $conn=new PDO("mysql:host=$servername;dbname=$database",$db_username,$db_password,
            array(PDO::MYSQL_ATTR_INIT_COMMAND => "set names utf8"));
        
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $stmt=$conn->prepare("SELECT score,last_req_time FROM member WHERE username=:username");
        $stmt->bindParam(':username', $exam_user);
        $stmt->execute();
        
        $rows=$stmt->fetchAll();
        $rowCount=$stmt->rowCount();
        
        $lastTS=$rows[0][1];
        $nowTS=date("Y-m-d H:i:s");
        $timeDiff=strtotime($nowTS)-strtotime($lastTS);
        
        if($timeDiff<$second_required_before_submit){
            $response['flag']='fail';
            $response['msg']='请认真答题,还有'.($second_required_before_submit-$timeDiff)."秒才能交卷.";
            echo json_encode($response);
            exit();
        }
        
        
        if($timeDiff>($conf_time_limit)){
            $response['flag']='fail';
            $response['msg']='你交卷超时'.($timeDiff-$conf_time_limit)."秒,请刷新页面重新考试.";
            echo json_encode($response);
            exit();
        }
        
        if($rowCount==1 && $rows[0][0]==-1){
            //算分
            $examKey=$_SESSION['exam_key'];
            $uAnswer=$_GET['ans'];
            $qcnt=strlen($uAnswer);
            if($qcnt!=strlen($examKey)){
                $response['flag']='fail';
                $response['msg']='试卷提交失败!错误信息:答案个数与试卷不匹配.';
                echo json_encode($response);
                exit();
            }
            
            $wrongs=array();
            $score=100;
            
            for($i=0;$i<$qcnt;$i++){
                if($uAnswer[$i]!=$examKey[$i]){
                    if(($i+1)<=$conf_choice_cnt){
                        //选择题
                        $score-=$conf_choice_score;
                    }else{
                        //判断题
                        $score-=$conf_judge_score;
                    }
                    array_push($wrongs, array('qnum'=>($i+1),'key'=>$examKey[$i]));
                }
            }
            
            $response['flag']='ok';
            $response['score']=$score;
            $response['msg']=$wrongs;
            echo json_encode($response);
            //向数据库写入分数
            
        }else{
            $response['flag']='fail';
            $response['msg']='试卷提交失败!错误信息:你已经完成考试,不得重复考试.';
            echo json_encode($response);
            exit();
        }
    }
    catch(PDOException $ex){
        $response['flag']='fail';
        $response['msg']=$ex->getMessage();
        echo json_encode($response);
        exit();
    }
    
    $conn=null;

}else{
    //未登录
    $response['flag']='fail';
    $response['msg']='试卷提交失败!错误信息:考生未登录.';
    echo json_encode($response);
    exit();
}

?>