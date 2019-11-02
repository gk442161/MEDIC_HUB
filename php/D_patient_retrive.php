<?php
    include 'mysql_login.php';
session_start();

    $conn=new mysqli($hostname,$username,$password,$database);
if($conn->connect_error) die($conn->connect_error);

$sql="SELECT JSON_PRETTY(account_no) as json, D_account_no FROM doctor_detail
        WHERE id=".$_SESSION['id']."";
$result=mysqli_query($conn,$sql);

if(!$result){
    echo mysqli_error($conn);
}
else{
    $row=mysqli_fetch_assoc($result);
    if ($row['json']==null) {
      echo"<p class='error'>no patient</p>";
    }
    else{   
    $accounts=explode(',',str_replace(['[',']'],'',$row['json']));//$acc_no;
    $_SESSION['doctor_account_no']=$row['D_account_no'];
    $username=$_SESSION['username'];
    
            //FOR NAME
    if(isset($_POST['fetch_name'])){
                foreach($accounts as $value){
                $sql2="SELECT CONCAT(name->>'$.firstname','  ',name->>'$.lastname') as fullname,phone_number FROM patient_detail WHERE P_account_no=$value";
                $result_2=mysqli_query($conn,$sql2);
                while($patient_name = $result_2->fetch_assoc()){

                
                echo"<div class='column'>
                <div class='card'><img src='uploads/$username.jpg' alt='Avatar' style='width:85%'>
                  <div class='container'>
                    <h4><b>".$patient_name['fullname']."</b></h4> 
                    <p>".$patient_name['phone_number']."</p> 
                    <p id='card_id' style='display:none'>$value</p>
                  </div>
                </div>
                </div>   ";
                }

            }
        }
      else{
          if(isset($_POST['card_id']))
          $sql="select details from diagnosis_report where D_account_no = (select D_account_no from doctor_detail where username='".$_SESSION['username']."') and P_account_no=".$_POST['card_id'];
          $result=mysqli_query($conn,$sql);
          echo mysqli_error($conn);
          echo mysqli_fetch_assoc($result)['details'];
            }

          }
    
}
?>
 
