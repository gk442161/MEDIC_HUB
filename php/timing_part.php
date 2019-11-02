<?php
include 'mysql_login.php';

$conn=new mysqli($hostname,$username,$password,$database);
if($conn->connect_error) die($conn->connect_error);

session_start();
$doc_username=$_SESSION['username'];
$result=mysqli_query($conn,"select D_account_no as acc from doctor_detail where username='$doc_username'");
if ($result) {
    $doctor_id=mysqli_fetch_assoc($result)['acc'];
    $result_apt=mysqli_query($conn,"select appointment_status, patients from appointment where doctor_id=$doctor_id");
    $obj_status=json_decode(mysqli_fetch_assoc($result_apt)['appointment_status']);
    $result_apt=mysqli_query($conn,"select appointment_status, patients from appointment where doctor_id=$doctor_id");
    $obj_pat=json_decode(mysqli_fetch_assoc($result_apt)['patients']);
}





//setting the time-zone
date_default_timezone_set('Asia/Kolkata');
$false_time=strtotime('13:16');
$false_date=strtotime('03-11-2019');
// var_dump($obj_pat);
// var_dump($obj_status);
$j=0;
$bt=strtotime($obj_status->start_time);
$et=strtotime($obj_status->end_time);
echo date('Y-m-d H:i',$false_time);
       // echo $bt<time();
//if doctor want to start the appointment
if (true) {//isset($_POST['status'])

    if (true) { //$_POST['status']=='start'
        $bt=strtotime($obj_status->start_time);
        $et=strtotime($obj_status->end_time);
        if($bt>$false_time){
            //don't run anything
                echo "<p>APPOINTMENT WILL START FROM $obj_status->start_time</p> ";
            }
        else{
            echo "else_part";
            if ($bt<=$false_time and $false_time<=$et) {
                     // start eliminating the events
                     echo "START ELIMINATING IF ";
                while($j<sizeof($obj_pat)){
                    echo " while_part 0";
                    echo $obj_pat[$j]->slot_time;
                    if (strtotime($obj_pat[$j]->slot_time)<=$false_time and $false_time<$et) {
                        //check the status of appointment
                        //whether the patient has made his checking done or not
                        echo "CHECK STATUS";
                        if ($obj_pat[$j]->status=='not_done'&& (strtotime($obj_pat[$j]->slot_time)+900)>$false_time+1) {
                            //do-nothing
                            echo "---do_nothing---";
                            break;
                            }
                        elseif ($obj_pat[$j]->status=='not_done'&& (strtotime($obj_pat[$j]->slot_time)+900)<=$false_time) {
                            //increase the time of every slot by 15min
                            echo "---INCREASE THE TIME---";
                            for ($i=$j+1; $i<(sizeof($obj_pat)) ; $i++) { 
                                echo "start";
                                if (strtotime($obj_pat[$i]->slot_time)>$false_time) {
                                    echo "---NO NEED OF INCREASSING THE TIME";
                                    break;
                                }
                                else{
                                    $new_time=date('H:i',strtotime($obj_pat[$i]->slot_time.'+15 min'));
                                    if (strtotime($obj_pat[$i]->slot_time)>$false_time and strtotime($obj_pat[$i]->slot_date)==$false_date) {
                                        echo "---don't need to be updated---";
                                        break;
                                    }
                                    elseif (strtotime($new_time)>$et-1) {
                                        echo "---DATE UPDATE---";
                                        echo $i." <".sizeof($obj_pat);
                                        echo $obj_pat[$i+1]->slot_date."   ".date('d-m-Y',$false_date+86400);
                                         if ( ($i+1)<(sizeof($obj_pat)) and strtotime($obj_pat[$i+1]->slot_date)==strtotime(date('d-m-Y',$false_date).'+1 days')) {
                                             echo "--UPDATING TIME FIRST";
                                            for($k=$i+1; $k<(sizeof($obj_pat)) ; $k++){
                                                $new_time_nxt_date=date('H:i',strtotime($obj_pat[$k]->slot_time.'+15 min'));
                                                $obj_pat[$k]->slot_time=$new_time_nxt_date;
                                                $sql="update appointment set patients ='".json_encode($obj_pat)."' where doctor_id =$doctor_id";//JSON_SET(patients, '$[$i].slot_time','$new_time')
                                                $result=mysqli_query($conn,$sql);
                                                echo mysqli_error($conn);
                                            }
                                         }
                                        $new_date=date('d-m-Y',strtotime($obj_pat[$i]->slot_date.'+1 days'));
                                        $obj_pat[$i]->slot_time=date('H:i',$bt);
                                        $obj_pat[$i]->slot_date=$new_date;
                                        $sql="update appointment set patients='".json_encode($obj_pat) ."'  where doctor_id =$doctor_id";//JSON_SET(patients, '$[$i].slot_time','".date('H:i',$bt)."','$[$i].slot_date','$new_date')
                                        $result=mysqli_query($conn,$sql);
                                        echo mysqli_error($conn);
                                        break;
                                    // }
                                    // else{
                                    //     $obj_pat[$i]->slot_time=$new_time;
                                    //     $sql="update appointment set patients =".json_encode($obj_pat)." where doctor_id =$doctor_id";//JSON_SET(patients, '$[$i].slot_time','$new_time')
                                    //     $result=mysqli_query($conn,$sql);
                                    //     echo mysqli_error($conn);
                                    // }
                                        }
                                
                                    elseif(strtotime($obj_pat[$i]->slot_date)==$false_date){
                                        echo "--TIME UPDATE--";
                                        $obj_pat[$i]->slot_time=$new_time;
                                        $sql="update appointment set patients ='".json_encode($obj_pat)."' where doctor_id =$doctor_id";//JSON_SET(patients, '$[$i].slot_time','$new_time')
                                        $result=mysqli_query($conn,$sql);
                                        echo mysqli_error($conn);
                                        }
                                    }
                                }
                                
                            }
                        elseif ($obj_pat[$j]->status=='done') {
                                 echo "elseif2_done";
                            }
                    }
                    else {
                        echo "there is no appoint in this timimng slot";
                        break;
                    }
                    $j=$j+1;
                  }
    
                // elseif (strtotime($appointment_list[$j]->time)>$false_time and $false_time<$et) {
                //          //do nothing
                //      }
                
              }
            elseif(strtotime(date('H:i',$false_time))>=$et){
                        //end the process
                        echo "process-ended";
                }
        }

    }
}
if (isset($_POST['extend'])) {
    # code...
}


?>