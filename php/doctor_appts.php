<?php
include 'mysql_login.php';


$conn=new mysqli($hostname,$username,$password,$database);
if($conn->connect_error) die($conn->connect_error);
session_start();
$doctor_id=mysqli_fetch_assoc(mysqli_query($conn,"select D_account_no from doctor_detail where username='".$_SESSION['username']."'"))['D_account_no'];//".$_SESSION['username']."

//sql to show the list
$sql="select patients from appointment where doctor_id=$doctor_id";
$result=mysqli_fetch_assoc(mysqli_query($conn,$sql))['patients'];
$object=json_decode($result);
//var_dump($object);

//sorting the json_data
date_default_timezone_set('Asia/Kolkata'); //making default time as asia/kolkata
$obj=json_decode($result);

usort($obj, function ($a, $b) {
return strtotime($a->slot_time) > strtotime($b->slot_time);
    });
usort($obj, function ($a, $b) {
    return strtotime($a->slot_date) > strtotime($b->slot_date);
        });
$obj_enc=json_encode($obj);
mysqli_query($conn,"UPDATE appointment set patients='$obj_enc' where doctor_id=".$doctor_id);
echo mysqli_error($conn);

if (isset($_POST['app_made'])) {

foreach ($obj as $value) {
   // var_dump($value->{'id'});
$sql2="SELECT CONCAT(name->>'$.firstname','  ',name->>'$.lastname') as fullname,phone_number FROM patient_detail WHERE P_account_no=".$value->{'p_account_no'};
$patient_name=mysqli_fetch_assoc(mysqli_query($conn,$sql2))['fullname'];
$patient_phone_no=mysqli_fetch_assoc(mysqli_query($conn,$sql2))['phone_number'];
$slot_id=$value->{'slot_id'};
$slot_time=date_parse($value->{'slot_time'});
$slot_date=$value->{'slot_date'};
$slot_status=$value->{'status'};
echo "<tr id=$slot_id class=$slot_status>
        <td >$patient_name</td>
        <TD>$patient_phone_no</TD>
        <td>".$slot_time['hour'].":".$slot_time['minute']."</td>
        <td>".$slot_date."</td>
        <TD id='main_status' onclick='updating_status(this)'>$slot_status</TD>
        </tr>";
// echo "<div><p style='background-color:lightgreen; width:20%;' id=$slot_id class=$slot_status>$slot_id $patient_name  $patient_phone_no ".$slot_time['hour'].":".$slot_time['minute']." </p></div>";
}


}
if(isset($_POST['x'])){//PROBLEM IN SEARCH ........
    $obj=json_decode($_POST['x']);
    echo $obj->slot_id;
    //echo 'ds';

    $count=0;
 
    $arg=mysqli_fetch_assoc(mysqli_query($conn,"select patients->>'$[*].slot_id' as slot_id from appointment where doctor_id=$doctor_id"))['slot_id'];
    $arg=json_decode($arg);
    foreach($arg as $values){
        if ($values==$obj->slot_id) {
            // $sql1="UPDATE appointment SET patients=json_set(patients,'$[$count]') where doctor_id=$d_account_no ";
            // $result=mysqli_query($conn,$sql1);
            // echo mysqli_error($conn);

            $sql="UPDATE `appointment` SET patients=json_set(patients,'$[$count].status','done') WHERE doctor_id=$doctor_id ";
            $result2=mysqli_query($conn,$sql);
            echo mysqli_error($conn);
           
        }
        $count++;
    }

    
}
?>
