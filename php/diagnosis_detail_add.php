<?php
include 'mysql_login.php';

$conn=new mysqli($hostname,$username,$password,$database);
if($conn->connect_error) die($conn->connect_error);

session_start();
//username[patient,doctor]

else{
    $sql_adding_detail="UPDATE diagnosis_report SET details=json_object('test',json_array(json_object('id',10,
                                                                                'date',CURRENT_TIMESTAMP,
                                                                                'name','TYPE_OF TEST',
                                                                                'outcome','OUTCOME_FROM_TEST',
                                                                                'performer','PERFORMER_NAME',
                                                                                'sub_test',json_array(json_object('sub_id','SUB_TEST_ID',
                                                                                                            'sub_name','SUB_TEST_NAME',
                                                                                                            'measurement','VALUES_OF_THE_TEST',
                                                                                                            'range','REFERENCE_RANGE')))),
                                                          'medicines',json_array(json_object('name','MEDICINE_NAME',
                                                                                        'dosage','DOSAGE',
                                                                                        'quantity','QUANTITY')),
                                                            'prescription','DOCTOR_PRESCRIPTION')

                                    WHERE diagnosis_no=1";
    

   
   

}