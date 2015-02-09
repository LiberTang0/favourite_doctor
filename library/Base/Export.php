<?php

class Base_Export {


public function getPatientData($filename="web.xls")
{
    $model = new Application_Model_Patient();
    $userModel = new Application_Model_User();
    $all_row= $model->fetchAll();
    foreach($all_row as $row)
    {
        if($row->getDoctorId())
        {
            $doctor_id = $row->getDoctorId();
       $docObject = $docModel->find($doctor_id);
       $doctor_name = $docObject->getFname();
        }
        else
        $doctor_name = "";



        $result[]=array(
            "Name"=>$row->getFname(),
            "Dr Name/Specialty"=>$doctor_name,
            "Phone"=>$row->getPhone(),
            "Email"=>$row->getEmail(),
            "Age"=>$row->getAge(),
            "Gender"=>$this->getGender($row->getGender()),
            "Status"=>$this->getStatus($row->getPatientStatus()),
            "Date/Time"=>$row->getAppointmentDate()."".$row->getAppointmentTime(),
            "Booking Date"=>date("Y-m-d",$row->getBookingDate())



        );
    }
$this->generatefile($result,$filename);

}

public function getWebAppointmentData($filename="web.xls")
{
    $model = new Application_Model_Appointment();
    $docModel = new Application_Model_Doctor();
    $all_row= $model->fetchAll("approve=1");
    foreach($all_row as $row)
    {
        if($row->getDoctorId())
        {
            $doctor_id = $row->getDoctorId();
       $docObject = $docModel->find($doctor_id);
       $doctor_name = $docObject->getFname();
        }
        else
        $doctor_name = "";

        

        $result[]=array(
            "Name"=>$row->getFname(),
            "Dr Name/Specialty"=>$doctor_name,
            "Phone"=>$row->getPhone(),
            "Email"=>$row->getEmail(),
            "Age"=>$row->getAge(),
            "Gender"=>$this->getGender($row->getGender()),
            "Status"=>$this->getStatus($row->getPatientStatus()),
            "Date/Time"=>$row->getAppointmentDate()."".$row->getAppointmentTime(),
            "Booking Date"=>date("Y-m-d",$row->getBookingDate())
           


        );
    }
$this->generatefile($result,$filename);
    
}

function generatefile($arr_data,$file_name)
{
    header("Content-Disposition: attachment; filename=\"$file_name\"");
header("Content-Type: application/vnd.ms-excel");
header("Content-Transfer-Encoding: binary ");
$flag=false;
 $xlsRow = 2;
foreach($arr_data as $row)
{
    if(!$flag)
    {
        //echo implode("\t",array_keys($row))."\n";
        $this->xlsBOF();
        $arkeys = array_keys($row);
        for($i=0;$i<count($arkeys);$i++)
        {
            $this->xlsWriteLabel(0,$i,$arkeys[$i]);
        }


        $flag = true;

    }
   // array_walk($row,"cleanData");
    //echo implode("\t", array_values($row))."\n";
    $arValues = array_values($row);
    for($z=0;$z<count($arValues);$z++)
    {
         //xlsWriteLabel($xlsRow,$z,$arValues[$z]);

       $this->xlsWriteLabel($xlsRow,$z,"$arValues[$z]");
    }
    $xlsRow++;
}
$this->xlsEOF();
}
function getStatus($status)
{
    switch($status)
    {
        case 'n':
            return "New";
            break;
        case 'e':
            return "Existing";
            break;
    }
}


function getGender($gender)
{
    switch($gender)
    {
        case 'm':
            return "Male";
            break;
        case 'f':
            return "Female";
            break;
    }
}


public function xlsBOF() {

    echo pack("ssssss", 0x809, 0x8, 0x0, 0x10, 0x0, 0x0);
    return;
}

public function xlsEOF() {
    echo pack("ss", 0x0A, 0x00);
    return;
}

public function xlsWriteNumber($Row, $Col, $Value) {
    echo pack("sssss", 0x203, 14, $Row, $Col, 0x0);
    echo pack("d", $Value);
    return;
}

public function xlsWriteLabel($Row, $Col, $Value ) {
    $L = strlen($Value);
    echo pack("ssssss", 0x204, 8 + $L, $Row, $Col, 0x0, $L);
    echo $Value;
return;
}

}
?>