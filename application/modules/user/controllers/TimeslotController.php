<?php

class User_TimeslotController extends Base_Controller_Action {

    public function preDispatch() {
        parent::preDispatch();
        $this->_helper->layout->setLayout('user');
    }
 
    public function indexAction() {
        $request = $this->getRequest();
        $post = $request->getPost();
        $post['month'] = $this->_getParam("month");
        $post['year'] = $this->_getParam("year");
        $usersNs = new Zend_Session_Namespace("members");
        $drid = $usersNs->doctorId;

        $Doctor = new Application_Model_Doctor();
        $docObject = $Doctor->find($drid);

        $Calendar = new Zend_Session_Namespace("calendar");
        if(isset($Calendar->CALDAY)){
            $month = date('m', $Calendar->CALDAY);
            $year = date('Y', $Calendar->CALDAY);
        }else{
            $month = date('m');
            $year = date('Y');
        }

        if (isset($post['month']) && $post['month'] > 0)
            $month = $post['month'];
        if (isset($post['year']) && $post['year'] > 0)
            $year = $post['year'];

         if ((isset($post['month']) && $post['month'] > 0) && (isset($post['year']) && $post['year'] > 0)){
             $today = mktime(0, 0, 0, $month, 1, $year);
             $Calendar->CALDAY =$today;
         }
       
        $monthArr = array(1 => "Jan", "Feb", "Mar", "Apr", "May", "June", "July", "Aug", "Sep", "Oct", "Nov", "Dec");

        $this->view->docObject = $docObject;
        $this->view->month = $month;
        $this->view->year = $year;
        $this->view->monthArr = $monthArr;
        $this->view->msg = base64_decode($this->_getParam('msg', ''));
    }

    function editAction() {
		
        $usersNs = new Zend_Session_Namespace("members");
        $drid = $usersNs->doctorId;
        $date = $this->_getParam('date');

        $timeSlots = "";

        $Doctor = new Application_Model_Doctor();
        $docObject = $Doctor->find($drid);



        $DoctorAppointment = new Application_Model_DoctorAppointmentCal();
        $object = $DoctorAppointment->fetchAll("doctor_id='{$drid}' AND slot_date='{$date}'");
        //$query = "SELECT slot_time FROM #__appointment_slot_master WHERE dr_id = ".$drid. " AND slot_date='".$_REQUEST['date']."'";
        if (!empty($object)) {
            foreach ($object as $obj) {
                $timeSlots .= date("H:i", strtotime($date . " " . $obj->getSlotTime())) . "\n";
            }
        }else{
            $TimeSlot  = new Base_Timeslot();
            $slotArray = $TimeSlot->getDoctorTimeSlots($drid, $date);
            $timeSlots = implode("\n", $slotArray);
        }	

        $this->view->docObject = $docObject;
        $this->view->date = $date;
        $this->view->timeSlots = $timeSlots;
    }

    
	function updateAction() {
        $request = $this->getRequest();
        $post = $request->getPost();
        $usersNs = new Zend_Session_Namespace("members");
        $drid = $usersNs->doctorId;
        $date = $post['date'];
        $DoctorAppointment = new Application_Model_DoctorAppointmentCal();
        $DoctorAppointment->delete("slot_date='" . $date . "' AND doctor_id = " . $drid);
        $AppointmentRemovedDate = new Application_Model_AppointmentRemovedDate();
        $AppointmentRemovedDate->delete("doctor_id='{$drid}'  AND slot_date='$date'");// remove the entry from deleted date
        if(isset($post['displayCheck']) && count($post['displayCheck'])){
            foreach ($post['displayCheck'] as $slot) {
                $timeSlot = date("H:i", strtotime($date . " " . trim($slot)));
				
				//error_log(date("H:i", strtotime("2011-10-14 01:00 PM")));
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
        $this->_helper->redirector('index', 'timeslot', "user", Array('drid' => $drid, 'month' => $m, 'year' => $y));
        exit();
    }

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
            $slotObject = $MasterSlot->fetchAll("doctor_id='{$drid}' AND week_number=1", "id ASC");
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

//prexit($options);
                 $MasterSlot->delete("doctor_id='{$drid}'");
                $weekDays = array('1'=>'MON','2'=>'TUE','3'=>'WED','4'=>'THU','5'=>'FRI','6'=>'SAT','7'=>'SUN');
                $j = 1;
                $weekNum = 1;
                for($i=1; $i<=7; $i++){

                     if(count($options['displayCheck'.$i])){
                         $slots = implode(',', $options['displayCheck'.$i]);
                     }else{
                         $slots = '';
                     }
                     // for time slot 1
                     $MasterSlot->setId(null);
                     $MasterSlot->setDoctorId($drid);
                     $MasterSlot->setSlotDay($weekDays[$j]);
                     $MasterSlot->setIsChecked($options['ischecked'.$i]);
                     $MasterSlot->setStartTime($options['stime'.$i]);
                     $MasterSlot->setEndTime($options['etime'.$i]);
                     $MasterSlot->setSlotInterval($options['time'.$i]);
                     $MasterSlot->setWeekNumber(1);
                     $MasterSlot->setDisplaySlots($slots);
                     $MasterSlot->save();

                     
                     $j++;
                 }
			  $j = 1;
                $weekNum = 1;
				 for($i=1; $i<=7; $i++){

                     if(count($options['displayCheck'.$i])){
                         $slots = implode(',', $options['displayCheck'.$i]);
                     }else{
                         $slots = '';
                     }

                     // for time slot 2
                     $MasterSlot->setId(null);
                     $MasterSlot->setDoctorId($drid);
                     $MasterSlot->setSlotDay($weekDays[$j]);
                     $MasterSlot->setIsChecked($options['ischecked'.$i]);
                     $MasterSlot->setStartTime($options['stime'.$i]);
                     $MasterSlot->setEndTime($options['etime'.$i]);
                     $MasterSlot->setSlotInterval($options['time'.$i]);
                     $MasterSlot->setWeekNumber(2);
                     $MasterSlot->setDisplaySlots($slots);
                     $MasterSlot->save();

                     $j++;
                 }
               
                $this->_helper->redirector('master-slot', 'timeslot', "user", Array('drid' => $drid, 'month' => $m, 'year' => $y));

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
            $dbSlotArray = explode(',', $object->getDisplaySlots());
        }

        $TimeSlot = new Base_Timeslot();
        $slotsArray = $TimeSlot->breakTimeslots($stime, $etime, $intval);

        $str = '';
        foreach($slotsArray as $st){
            $checked = "";
			
            if(!empty($dbSlotArray)){
                if(in_array($st1, $dbSlotArray))$checked = "checked='checked'";
            }else{
                $checked = "checked='checked'";
            }
            $str .= "<input type='checkbox' name='displayCheck{$num}[]' {$checked} value='{$st}'>{$st}<br />";
        }
        $return['slots'] = stripslashes($str);
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
                if(in_array($st1, $dbSlotArray))$checked = "checked='checked'";
            }else{
                $checked = "checked='checked'";
            }
            $str .= "<input type='checkbox' name='displayCheck{$num}[]' {$checked} value='{$st}'>{$st}<br />";
        }
        $return['slots'] = stripslashes($str);

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