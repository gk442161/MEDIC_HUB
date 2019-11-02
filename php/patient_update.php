<?php
        session_start();
        include 'mysql_login.php';

        $conn=new mysqli($hostname,$username,$password,$database);
        if($conn->connect_error) die($conn->connect_error);
        $sql="select username from patient_detail where username='".$_POST['patient_username']."'";
        $verify=mysqli_fetch_assoc(mysqli_query($conn,$sql))['username'];
        if($verify==NULL){
            echo "<p>NO SUCH USER</p>";

        }
        else{$sql="select P_account_no from patient_detail where username='".$_POST['patient_username']."'";
                $P_account_no=mysqli_fetch_assoc(mysqli_query($conn,$sql))['P_account_no'];

                $sql3="SELECT JSON_LENGTH(doctor_appointed) as p_length 
                        from patient_detail 
                        WHERE username='".$_POST['patient_username']."'" ;
                $result1=mysqli_query($conn,$sql3);  
                
                $sql4="SELECT JSON_LENGTH(account_no) as d_length ,D_account_no
                        from doctor_detail
                        WHERE username='".$_SESSION['username']."'" ;
                $result2=mysqli_query($conn,$sql4);  
                //echo(mysqli_error($conn))  ;
                $l1=(int)mysqli_fetch_assoc($result1)['p_length'];

                $_SESSION['doctor_account_no']=mysqli_fetch_assoc($result2)['D_account_no'];
                $l2=(int)mysqli_fetch_assoc($result2)['d_length'];

                //inserting doctor and patient  account_no
                if($l1==0 && $l2==0 ){
                    $sql2="UPDATE `patient_detail` as p,`doctor_detail` as d SET 
                    p.doctor_appointed=JSON_ARRAY(".$_SESSION['doctor_account_no'].") ,
                    d.account_no=JSON_ARRAY(".$P_account_no.")
                    WHERE p.username='".$_POST['patient_username']."' and d.username='".$_SESSION['username']."'";
                    $result2=mysqli_query($conn,$sql2);
                    echo mysqli_error($conn);
                    echo 'k1';

                }  
                elseif($l1>0 && $l2==0 ){
                    $sql2="UPDATE `patient_detail` as p,`doctor_detail` as d SET 
                    p.doctor_appointed=JSON_ARRAY_APPEND(p.doctor_appointed->>'$','$',".$_SESSION['doctor_account_no'].") ,
                    d.account_no=JSON_ARRAY(".$P_account_no.")
                    WHERE p.username='".$_POST['patient_username']."' and d.username='".$_SESSION['username']."'";
                    $result2=mysqli_query($conn,$sql2);
                    echo mysqli_error($conn);
                    echo 'k2';
                }
                elseif($l1==0 && $l2>0 ){
                    $sql2="UPDATE `patient_detail` as p,`doctor_detail` as d SET 
                    p.doctor_appointed=JSON_ARRAY(".$_SESSION['doctor_account_no'].") ,
                    d.account_no=JSON_ARRAY_APPEND(d.account_no->>'$','$',".$P_account_no.")
                    WHERE p.username='".$_POST['patient_username']."' and d.username='".$_SESSION['username']."'";//";                                                     
                    $result2=mysqli_query($conn,$sql2);
                    echo mysqli_error($conn);
                    echo 'k3';
                }
                elseif($l1>0 && $l2>0 ) {
                    $sql2="UPDATE `patient_detail` as p,`doctor_detail` as d SET 
                    p.doctor_appointed=JSON_ARRAY_APPEND(p.doctor_appointed->>'$','$',".$_SESSION['doctor_account_no'].") ,
                    d.account_no=JSON_ARRAY_APPEND(d.account_no->>'$','$',".$P_account_no.")
                    WHERE p.username='".$_POST['patient_username']."' and d.username='".$_SESSION['username']."'";//";                                                     
                    $result2=mysqli_query($conn,$sql2);
                    echo mysqli_error($conn);
                    echo 'k4';
                }
            else {
                 echo"sorry";
                }
        }
