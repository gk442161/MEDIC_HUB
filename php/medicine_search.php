<?php
include 'mysql_login.php';

$conn=new mysqli($hostname,$username,$password,$database);
if($conn->connect_error) die($conn->connect_error);


$output=array();

if ($_GET['search']!='' and  isset($_GET['search'])) {//
    

$result=mysqli_query($conn,"select medicines->>'$[*].disease' as med from medicine");
$arr=json_decode(mysqli_fetch_assoc($result)['med']);
$count=0;
$limit=15;
foreach($arr as $value) {
    //cho $value;
        if(strstr(strtolower($value),strtolower(get_get($conn, $_GET['search']))) and $count<$limit ){ //
        // $name=json_decode($row['name']);
        $output[]=$value;
        $count++;
        }     
    }

}
if($output==null)
{
echo "no result found";
}
else{
    echo json_encode($output);
}

