<?php
include 'mysql_login.php';

 $conn=new mysqli($hostname,$username,$password,$database);
 if($conn->connect_error) die($conn->connect_error);


//  $sql="SELECT JSON_PRETTY(account_no) as json FROM doctor_detail
//         WHERE id=1";
//         $result=mysqli_query($conn,$sql);
//         $row=mysqli_fetch_assoc($result);
//         $accounts=explode(',',str_replace(['[',']'],'',$row['json']));
//         echo $accounts[2];
else{
session_start();
//THIS IS FOR FETCHING ALL CARDS
                if(isset($_POST['fetch_name'])){
                    
                $sql="select patient_detail.doctor_appointed->>'$[*]' as json FROM patient_detail where username='".$_SESSION['username']."'";
                $result=mysqli_query($conn,$sql);
                $row=mysqli_fetch_assoc($result);
                $username=$_SESSION['username'];
                $accounts=explode(',',str_replace(['[',']',],'',$row['json']));
                //echo $accounts[0];

                foreach($accounts as $value){
                    //echo gettype((int)$value).$value;
                    $result3=mysqli_query($conn,"select CONCAT(name->>'$.firstname','  ',name->>'$.lastname') as fullname, phone_number from doctor_detail where D_account_no=$value");
                    echo mysqli_error($conn);
                    $row2=mysqli_fetch_assoc($result3);

                    
                    echo"<div class='column'>
                                <div class='card'><img src='uploads/$username.jpg' alt='Avatar' style='width:85%'>
                                <div class='container'>
                                    <h4><b>".$row2['fullname']."</b></h4> 
                                    <p>".$row2['phone_number']."</p> 
                                    <p id='card_id' style='display:none'>$value</p>
                                </div>
                                </div>
                                </div>   ";
                }
            }
                elseif(isset($_POST['card_id'])){
                    $sql="select details from diagnosis_report where P_account_no = (select P_account_no from patient_detail where username='".$_SESSION['username']."') and D_account_no=".$_POST['card_id'];
                    $result=mysqli_query($conn,$sql);
                    echo mysqli_error($conn);
                   // $details=json_decode(mysqli_fetch_assoc($result)['details']) ;

                //echo diagnosis report
                    
                    
                    $obj =mysqli_fetch_assoc($result)['details'];
                    echo $obj;
                    // $str="";
                    
                    // $str_med_start="<div id='medicines'>";
                    // $str_med_end="</div>";
                    // $str_test_start="<div id='sub_test'>";
                    // $str_test_end="</div>";
                    // foreach($obj->test as $key=>$value){
                    //     if($value=='sub_test'){
                    //         foreach($value as $ky=>$val){
                    //             $str_test_start.="SUB TEST $val->sub_name <br> MEASUREMENT $val->measurement".$str_test_end;
                    //         }
                    //     }
                    //     if($value=='performer'){
                    //          $str_performer="<div id='performer'>$value->performer</div>";
                    //     }
                    //     $str="<p id='diagnosis_detail'><div id='tests'>TEST ID: $value->id<br>
                    //                                                     </div></p>";
                    //}
                }
else {
    header(402);
}
//THIS IS FETCHING INDIVUDUAL CARD DETAIL
// $sql2="SELECT "
            }
?>