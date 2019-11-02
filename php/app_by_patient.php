<?php
include 'mysql_login.php';

$conn=new mysqli($hostname,$username,$password,$database);
if($conn->connect_error) die($conn->connect_error);
session_start();

$status=true;
$result=mysqli_query($conn,"select D_account_no as acc, name from doctor_detail where email='".$_POST['D_username']."'");
if ($result) {
    if(isset($_POST['status'])){
        $status=false;
    }
    else{
    $status=true;}
    $doctor=mysqli_fetch_array($result);
    $patient=mysqli_fetch_assoc(mysqli_query($conn,"select P_account_no as acc,name from patient_detail where username='".$_SESSION['username']."'"));
    $appointment=mysqli_fetch_assoc(mysqli_query($conn,"select * from appointment where doctor_id=".$doctor['acc']));
}
else{
    echo mysqli_error($conn);
}

if(!$status and isset($_POST['status'])){
    $html='';
    $appt_patients_obj=json_decode($appointment['patients']);
    $sloted_times=array();
    foreach($appt_patients_obj as $key=>$val){
        if($appt_patients_obj[$key]->p_account_no==$patient['acc']){
         
         $sloted_times[]=$appt_patients_obj[$key]->slot_id;
         $html.=" <option value='".$appt_patients_obj[$key]->slot_id."'>".$appt_patients_obj[$key]->slot_time."</option>";
    }
   
}
$jsn=[
    'status'=>'success','text'=>$html
];
echo(json_encode($jsn));
}


if ($status) {
    $appt_status_obj=json_decode($appointment['appointment_status']);
    date_default_timezone_set('Asia/Kolkata'); //making default time as asia/kolkata

    $arr=array();
    for ($i=strtotime($appt_status_obj->start_time); $i <=strtotime($appt_status_obj->end_time) ; $i=$i+900) { 
        $arr[]=date("H:i",$i);
            }

    $slot_time=$arr[$_POST['slot_id']-1];
    var_dump($arr) ;



    //add patient to the appointment
    if(isset($_POST['add'])){
        $date=date('d-m-Y',strtotime(date('d-m-Y',time())."+1 days"));//add +1 days  
        //$sql="select doctor_id ,patients from appointment where doctor_id=$d_account_no";
             if($appointment['doctor_id']==null){
                 echo "---1---";
                 $sql1="INSERT INTO appointment(doctor_id, patients) VALUES (".$doctor['acc'].",json_array(json_object('slot_id',".$_POST['slot_id'].",'p_account_no',$p_account_no,'slot_time','$slot_time','slot_date','$date','status','not_done')))";
            }
        else{
            var_dump($appointment);
            if($appointment['patients']==null or $appointment['patients']=='null'){
                echo "---2----";
            $sql1="UPDATE appointment SET patients= (json_array(json_object('slot_id',".$_POST['slot_id'].",'p_account_no',".$patient['acc'].",'slot_time','$slot_time','slot_date','$date','status','not_done'))) WHERE doctor_id=".$doctor['acc'];
                }
            else{
                echo "----3---";
                $sql1="UPDATE `appointment` SET patients=json_array_append(patients,'$',json_object('slot_id',".$_POST['slot_id'].",'p_account_no',".$patient['acc'].",'slot_time','$slot_time','slot_date','$date','status','not_done')) WHERE doctor_id=".$doctor['acc'];
            }
         }
    $result=mysqli_query($conn,$sql1);
    if($result){
        echo "YOUR APPOINTMENT IS MADE TO ";
        echo mysqli_error($conn);
    }
    else{
        echo mysqli_error($conn);
    }


        }
    if (isset($_POST['remove'])) {
    
       
 
    //$arg=mysqli_fetch_assoc(mysqli_query($conn,"select patients->>'$[*].slot_id' as slot_id from appointment where doctor_id=$d_account_no"))['slot_id'];
    //$appt_patients_obj=json_decode($appointment['patients']);

    $pat=json_decode($appointment['patients']);
    var_dump($pat);
    foreach($pat as $key=>$val){
        if($pat[$key]->slot_time==$slot_time and $pat[$key]->p_account_no==$patient['acc']){
         echo "found";
         unset($pat[$key]);
         $pat_new=array_values($pat);
         var_dump($pat_new);
         $into_str=json_encode($pat_new);
         echo $into_str;
         $sql1="UPDATE appointment SET patients='$into_str' where doctor_id=".$doctor['acc'] ;
         $result=mysqli_query($conn,$sql1);
         echo mysqli_error($conn);
    }
}
    // foreach($appt_patients_obj as $key=>$values){
    //     if ($values->slot_id==$_POST['slot_id']) {
    //         unset($appt_patients_obj[$key]);
    //         $into_str=json_encode($appt_patients_obj);

           
    //     }
        
    // }

    
}

//sorting the json_data

// $object=json_decode($appointment['patients']);

// usort($object, function ($a, $b) {
// return $a->slot_id > $b->slot_id;
//     });

// $object=json_encode($object);
// mysqli_query($conn,"UPDATE appointment set patients='$object' where doctor_id=".$doctor['acc']);
// echo mysqli_error($conn);
 }


// $sql="select patients from appointment where doctor_id=$d_account_no";
// $result=mysqli_fetch_assoc(mysqli_query($conn,$sql))['patients'];
// $object=json_decode($result);

// usort($object, function ($a, $b) {
//     return $a->slot_id > $b->slot_id;
// });

// $object=json_encode($object);
// mysqli_query($conn,"UPDATE appointment set patients='$object' where doctor_id=$doctor_id");
// echo mysqli_error($conn);
//if($_POST[''])
//$sql="INSERT INTO `appointment`(`doctor_id`, `patients`) VALUES (2,json_array(json_object('id',1,'p_account_no',9)))";


