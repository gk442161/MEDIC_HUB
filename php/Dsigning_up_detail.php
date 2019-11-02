<?php
    include 'mysql_login.php';

    $conn=new mysqli($hostname,$username,$password,$database);
if($conn->connect_error) die($conn->connect_error);


var_dump(json_decode($_POST['json']));
var_dump($_POST['username']);


var_dump($_FILES);
if(isset($_POST['reset'])){

}
if(isset($_POST['json'])){
    $obj=json_decode($_POST['json']);
    $status=array(1=>'insertion_error',2=>'success');
    unset($obj->profileimage);
    unset($obj->licensesimg);
    $firstname=mysqli_real_escape_string($conn,$obj->firstname);
    $lastname=mysqli_real_escape_string($conn,$obj->lastname);
    $email=mysqli_real_escape_string($conn,$obj->email); //
    $username=mysqli_real_escape_string($conn,$obj->username);
    $phone_number=mysqli_real_escape_string($conn,$obj->phone_number);
    $password=mysqli_real_escape_string($conn,$obj->password);
    
    var_dump($obj);
    $name=Array('firstname'=>$firstname,'lastname'=>$lastname);
    $hased_password=md5($password);
    $id='';
    $row= array();
    //SQL QUERIES
    $sql_insert_in_mainlogin="INSERT INTO main_login(username,password,email,profession) values('$username','$hased_password','$email',1)";
    $sql_fetch_id="SELECT id FROM main_login WHERE username='$username'";
    
    
    
    
    //EXECUTING
    
    
    $result=mysqli_query($conn,$sql_insert_in_mainlogin);
    if (!$result) {
        echo "querry1 failed ".mysqli_error($conn);
    } else {
        $result2=mysqli_query($conn,$sql_fetch_id);
        if(!$result2){echo "querry2 failed ".mysqli_error($conn);}
            else{
                $row=mysqli_fetch_assoc($result2);
                $id=$row['id'];
                
                $sql_insert_in_doctorDetail="INSERT INTO doctor_detail(`username`,`email`,`id`,`phone_number`,`name`) values('$username','$email',$id,$phone_number ,JSON_OBJECT('firstname','".$name['firstname']."','lastname','".$name['lastname']."'))";
    
                $result3=mysqli_query($conn,$sql_insert_in_doctorDetail);
                if (!$result3) {echo "querry3 failed ".mysqli_error($conn);}
                else{$enc_obj=mysqli_real_escape_string($conn,json_encode($obj));
                    echo $enc_obj;
                    $sql_insert_mainDetail="UPDATE doctor_detail SET cradential_detail='$enc_obj' where id=$id and username='$username'";
                    $result4=mysqli_query($conn,$sql_insert_mainDetail);
                    if (!$result4) {echo "querry4 failed ".mysqli_error($conn);}
                    else{
                        echo $status[2];
                    }
    
                }
            }
    } 
}
echo($_FILES);
if( $_FILES ){
    $target_dir ='../uploads/';
    $target_file_P= $target_dir . basename($_FILES["profileimage"]["name"]);
    $target_file_L= $target_dir . basename($_FILES["licensesimg"]["name"]);
    $uploadOk = 1;
    $imageFileType_1 = strtolower(pathinfo($target_file_P,PATHINFO_EXTENSION));
    $imageFileType_2 = strtolower(pathinfo($target_file_L,PATHINFO_EXTENSION));
    // Check if image file is a actual image or fake image
    
        $check_1 = getimagesize($_FILES["profileimage"]["tmp_name"]);
        $check_2 = getimagesize($_FILES["licensesimg"]["tmp_name"]);
        if($check_1!== false ) {
            echo "File is an image - " . $check_1["mime"] . ".";
            $uploadOk = 1;
        } else {
            echo "File is not an image.";
            $uploadOk = 0;
        }
    
    // Check if file already exists

    $new_target_file=$target_dir.$_POST['username'];
    if (file_exists($new_target_file)) {
        echo "Sorry, file already exists.";
        $uploadOk = 0;
    }
    // Check file size
    if ($_FILES["profileimage"]["size"] > 500000 and $_FILES["lincensesimg"]["size"] > 500000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }
    // Allow certain file formats
    if($imageFileType_1 != "jpg" && $imageFileType_1 != "png" && $imageFileType_1 != "jpeg"
    && $imageFileType_1 != "gif" ) {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }
    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    // if everything is ok, try to upload file
    } else {
        
        
         $new_target_file=$target_dir.$_POST['username'];
         mkdir($new_target_file);
        if (move_uploaded_file($_FILES["profileimage"]["tmp_name"], $new_target_file.'/profile.'.$imageFileType_1)) {
            echo "The file_1 has been uploaded.";
        } else {
            echo "Sorry, there was an error uploading your file.";
        } 
        if (move_uploaded_file($_FILES["licensesimg"]["tmp_name"], $new_target_file.'/license.'.$imageFileType_2)) {
            echo "The file_1 has been uploaded.";
        } else {
            echo "Sorry, there was an error uploading your file.";
        } 
    }
    

}






?>