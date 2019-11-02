<?php
include 'mysql_login.php';

$conn=new mysqli($hostname,$username,$password,$database);
if($conn->connect_error) die($conn->connect_error);


$output=array();
if ($_GET['search']!='' and isset($_GET['search'])) {
    
$val=$_GET['search'];
$result=mysqli_query($conn,"select username, email, name from doctor_detail where username LIKE '%".$val."%' 
                                or name->>'$.firstname' like '%".$val."%' or 
                                email like '%".$val."%' ");



if(mysqli_num_rows($result)>0){
    while($row=mysqli_fetch_assoc($result)){
        // $name=json_decode($row['name']);
        $output[]=$row;
    }
    echo json_encode($output)     ;      


}
else{
echo "sorry no such doctor username exist";
}
}
else{
    echo "internal error";
}

$result->free();