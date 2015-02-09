<?php

class Admin_TimeslotController extends Base_Controller_Action {

    public function indexAction() {
        $this->view->title = "Admin Panel- Time Slots";
        $this->view->headTitle("Admin Panel");

        $request = $this->getRequest();
        //$post = $request->getPost();
        $post['month'] = $this->_getParam("month");
        $post['year'] = $this->_getParam("year");

        $drid = $this->_getParam('drid');

        $Doctor = new Application_Model_Doctor();
        $docObject = $Doctor->find($drid);

        $month = date('m');
        $year = date('Y');

        if (isset($post['month']) && $post['month'] > 0)
            $month = $post['month'];
        if (isset($post['year']) && $post['year'] > 0)
            $year = $post['year'];
       
        $monthArr = array(1 => "Jan", "Feb", "Mar", "Apr", "May", "June", "July", "Aug", "Sep", "Oct", "Nov", "Dec");

        $this->view->docObject = $docObject;
        $this->view->month = $month;
        $this->view->year = $year;
        $this->view->monthArr = $monthArr;
        $this->view->msg = base64_decode($this->_getParam('msg', ''));
    }

    function editAction() {
        $drid = $this->_getParam('drid');
        $date = $this->_getParam('date');

        $timeSlots = "";

        $Doctor = new Application_Model_Doctor();
        $docObject = $Doctor->find($drid);

        $TimeSlot  = new Base_Timeslot();
        $slotArray = $TimeSlot->getDoctorTimeSlots($drid, $date);
        $timeSlots = implode("\n", $slotArray);
    
        $this->view->docObject = $docObject;
        $this->view->date = $date;
        $this->view->timeSlots = $timeSlots;
    }

    function updateAction() {

        $request = $this->getRequest();
        $post = $request->getPost();
        $drid = $post['drid'];
        $date = $post['date'];
        $displayCheck = $post['displayCheck'];
        $DoctorAppointment = new Application_Model_DoctorAppointmentCal();
        $DoctorAppointment->delete("slot_date='" . $date . "' AND doctor_id = " . $drid);
        $AppointmentRemovedDate = new Application_Model_AppointmentRemovedDate();
        $AppointmentRemovedDate->delete("doctor_id='{$drid}'  AND slot_date='$date'");// remove the entry from deleted date
        
        if(is_array($displayCheck)){
            foreach ($displayCheck as $slot) {
                $timeSlot = date("H:i", strtotime($date . " " . trim($slot)));
                if (trim($slot) != "") {
                    $DoctorAppointment->setDoctorId($drid);
                    $DoctorAppointment->setSlotTime($timeSlot);
                    $DoctorAppointment->setSlotDate($date);
                    $DoctorAppointment->save();
                }
            }
        }else{

            $object = $AppointmentRemovedDate->fetchRow("doctor_id='{$drid}'  AND slot_date='$date'");
            if(empty($object)){
                $AppointmentRemovedDate->setDoctorId($drid);
                $AppointmentRemovedDate->setSlotDate($date);
                $AppointmentRemovedDate->save();
            }
        }
        
        $m = date("m", strtotime($date));
        $y = date("Y", strtotime($date));
        $this->_helper->redirector('index', 'timeslot', "admin", Array('drid' => $drid, 'month' => $m, 'year' => $y));
        exit();
    }
/*
    function copyAction() {
        $request = $this->getRequest();
        $post = $request->getPost();
        $drid = $post['drid'];
        
        
        $no_of_week_to_copy = $post['no_of_week'];

        $DoctorAppointment = new Application_Model_DoctorAppointmentCal();

        for ($i = 0; $i < 7; $i++) { //##### Copy previous week slot data to next week #####//
            $copy_source_date = date("Y-m-d", mktime(0, 0, 0, date('m'), (date('d') - (date('w') - 1)) - 7 + $i, date('Y')));

            for ($week = 0; $week < $no_of_week_to_copy; $week++) {
                $target_date = date("Y-m-d", mktime(0, 0, 0, date('m'), (date('d') - (date('w') - 1)) + ($i + ($week * 7)), date('Y')));

                $DoctorAppointment->delete("slot_date='{$target_date}'");

                $object = $DoctorAppointment->fetchAll("doctor_id ={$drid} AND slot_date='{$copy_source_date}'");
                if (!empty($object)) {
                    foreach ($object as $obj) {
                        $DoctorAppointment->setDoctorId($drid);
                        $DoctorAppointment->setSlotTime($obj->getSlotTime());
                        $DoctorAppointment->setSlotDate($target_date);
                        $DoctorAppointment->save();
                    }
                }
                //$query = "INSERT INTO dih_appointment_slot_master SELECT dr_id, slot_time,'{$target_date}' FROM dih_appointment_slot_master WHERE dr_id ={$sobi2Id} AND slot_date='{$copy_source_date}'";
            }
        }
        $this->_helper->redirector('index', 'timeslot', "admin", Array('drid' => $drid, 'month' => date("m"), 'year' => date("Y")));
        exit();
    }

    function weeklyAction() {

        $drid = $this->_getParam('drid');
        $startdate = $this->_getParam('startdate');
        $month = $this->_getParam('month');
        $year = $this->_getParam('year');

        $drid = $this->_getParam('drid');
        $Doctor = new Application_Model_Doctor();
        $docObject = $Doctor->find($drid);
        $this->view->docObject = $docObject;

        $this->view->startdate = $startdate;
        $this->view->month = $month;
        $this->view->year = $year;
    }

    function createWeeklyAction() {
        $request = $this->getRequest();
        $post = $request->getPost();
        $drid = $post['drid'];

        $DoctorAppointment = new Application_Model_DoctorAppointmentCal();
        $returnDate = date("Y-m-d");
        for ($i = 1; $i <= 7; $i++) {
            if (isset($post["day" . $i]) && $post["day" . $i] != '') {

                $startTime = $post["day" . $i] . " " . $post["stime" . $i];
                $endTime = $post["day" . $i] . " " . $post["etime" . $i];
                $timeseed = $post["time" . $i];

                $query_val = array();
                for ($t = strtotime($startTime); $t <= strtotime($endTime); $t = ($t + (60 * $timeseed))) {
                    $timeSlot = date('H:i', $t);
                    $query_val[] = "( '" . $drid . "','" . $timeSlot . "','" . $post["day" . $i] . "')";
                    $DoctorAppointment->delete("slot_time='{$timeSlot}' AND slot_date='" . $post["day" . $i] . "' AND doctor_id = " . $drid);

                    $DoctorAppointment->setDoctorId($drid);
                    $DoctorAppointment->setSlotTime($timeSlot);
                    $DoctorAppointment->setSlotDate($post["day" . $i]);
                    $DoctorAppointment->save();
                }
                if (count($query_val)) {
                    $returnDate = $post["day" . $i];
                }
               
            }
        }
        $m = date("m", strtotime($returnDate));
        $y = date("Y", strtotime($returnDate));
        $this->_helper->redirector('index', 'timeslot', "admin", Array('drid' => $drid, 'month' => $m, 'year' => $y));
        exit();
    }
*/
    function masterSlotAction() {

        $drid = $this->_getParam('drid');
        $request = $this->getRequest();
        $options = $request->getPost();

        $form = new Admin_Form_MasterSlot();
        $elements = $form->getElements();
        $form->clearDecorators();
        foreach ($elements as $element) {
            $element->removeDecorator('label');
            $element->removeDecorator('row');
            $element->removeDecorator('data');
            $element->removeDecorator('tag');
        }

        $Doctor = new Application_Model_Doctor();
        $docObject = $Doctor->find($drid);
        $MasterSlot = new Application_Model_MasterTimeslot();
        if($drid > 0){
            $slotObject = $MasterSlot->fetchAll("doctor_id='{$drid}'", "id ASC");
            $i = 1;
            $setOptions = array();
            foreach($slotObject as $slots){

                $setOptions['id'.$i] = $slots->getId();
                $setOptions['ischecked'.$i] = $slots->getIsChecked();
                $setOptions['stime'.$i] = $slots->getStartTime();
                $setOptions['etime'.$i] = $slots->getEndTime();
                $setOptions['time'.$i] = $slots->getSlotInterval();
                $i++;
            }
            $form->populate($setOptions);
        }



        if ($request->isPost()) {
             if ($form->isValid($options)) {
             	$MasterSlot->delete("doctor_id='{$drid}'");
                $weekDays = array('1'=>'MON','2'=>'TUE','3'=>'WED','4'=>'THU','5'=>'FRI','6'=>'SAT','7'=>'SUN');
                $day = 1;
                $weekNum = 1;
                for($i=1; $i<=14; $i++){
					 if($i%8==0)
					 	$weekNum=$weekNum+1;
                     if($day%8==0)
                     	$day=1;
                     

                     $MasterSlot->setId(null);
                     $MasterSlot->setDoctorId($drid);
                     $MasterSlot->setSlotDay($weekDays[$day]);
                     $MasterSlot->setIsChecked($options['ischecked'.$i]);
                     $MasterSlot->setStartTime($options['stime'.$i]);
                     $MasterSlot->setEndTime($options['etime'.$i]);
                     $MasterSlot->setSlotInterval($options['time'.$i]);
                     $MasterSlot->setWeekNumber($weekNum);
                     if(count($options['displayCheck'.$i])){
                         $slots = implode(',', $options['displayCheck'.$i]);
                     }else{
                         $slots = '';
                     }
                     $MasterSlot->setDisplaySlots($slots);
                     $MasterSlot->save();
                     $day++;
                 }
                 
                $this->_helper->redirector('master-slot', 'timeslot', "admin", Array('drid' => $drid, 'month' => $m, 'year' => $y));

             }
        }

        $this->view->docObject = $docObject;
        $this->view->form = $form;
        
    }

     function getDeletedSlotsAction() {

        $id = $this->_getParam('id');
        $stime = $this->_getParam('stime');
        $etime = $this->_getParam('etime');
        $intval = $this->_getParam('intval');
        $num = $this->_getParam('num');
        $slots = "";
        $dbSlotArray = array();
        $MasterSlot = new Application_Model_MasterTimeslot();
        $object = $MasterSlot->find($id);
        if(!empty($object) && $object->getDisplaySlots()!=''){
            //$slots = str_replace(",","\n",$object->getDisplaySlots());
            $dbSlotArray = explode(',', $object->getDisplaySlots());
        }
        
        $TimeSlot = new Base_Timeslot();
        $slotsArray = $TimeSlot->breakTimeslots($stime, $etime, $intval);
        //$slots = implode("\n", $slotsArray);
        
        $str = '';
        foreach($slotsArray as $st){
            $checked = "";
            if(!empty($dbSlotArray)){
                if(in_array($st, $dbSlotArray))$checked = "checked='checked'";
            }else{
                $checked = "checked='checked'";
            }
            $str .= "<input type='checkbox' name='displayCheck{$num}[]' {$checked} value='{$st}'>{$st}<br />";
        }
        $return['slots'] = stripslashes($str);
//        $return['slots'] = $slots;
        $return['num'] = $num;
        $return['id'] = $id;
        echo json_encode($return);
        exit;
     }

     function makeSlotsAction() {

        $id = $this->_getParam('id');
        $stime = $this->_getParam('stime');
        $etime = $this->_getParam('etime');
        $intval = $this->_getParam('intval');
        $num = $this->_getParam('num');
        $slots = "";
        $TimeSlot = new Base_Timeslot();
        $slotsArray = $TimeSlot->breakTimeslots($stime, $etime, $intval);

        $str = '';
        foreach($slotsArray as $st){
            $checked = "";
            if(!empty($dbSlotArray)){
                if(in_array($st, $dbSlotArray))$checked = "checked='checked'";
            }else{
                $checked = "checked='checked'";
            }
            $str .= "<input type='checkbox' name='displayCheck{$num}[]' {$checked} value='{$st}'>{$st}<br />";
        }
        $return['slots'] = stripslashes($str);
        
        //$slots = implode("\n", $slotsArray);
        //$return['slots'] = $slots;
        $return['num'] = $num;
        $return['id'] = $id;
        echo json_encode($return);
        exit;
     }

     function saveDisplaySlotsAction() {

        $id = $this->_getParam('id');
        $slots = $this->_getParam('slots');
        $stime = $this->_getParam('stime');
        $etime = $this->_getParam('etime');
        $intval = $this->_getParam('intval');

        $MasterSlot = new Application_Model_MasterTimeslot();
        $object = $MasterSlot->find($id);
        if(!empty($object)){
            //$slots = ereg_replace("[,]+", ",",str_replace(array("\r","\n"), ",", $slots));
            $slots = trim($slots, ',');
            $object->setStartTime($stime);
            $object->setEndTime($etime);
            $object->setSlotInterval($intval);
            $object->setDisplaySlots($slots);
            $object->save();
        }
        $return['id'] = $id;
        echo json_encode($return);
        exit;
     }
}

?>