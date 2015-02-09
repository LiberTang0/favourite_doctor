<?php

class Base_Timeslot {

     public function getAppointmentAvailability($post)//Get appointment availablity details based on Dr
	{
	
		$settings = new Admin_Model_GlobalSettings();
		$locale = $settings->settingValue('locale');
		setlocale(LC_ALL, $locale);
        
		$returnArray = array();
		$todayDate	=	mktime(0,0,0, date("m"), date("d"), date("Y"));
                
		$drid = $post['drid'];
		$type = $post['type']; //Request is from serch result or profile page

		if(!isset($post['disp'])){
			$post['disp'] = 'more';
		}
		if (isset ( $post['start_date'] )) {
			$start_date = $post ['start_date'];

			$dateArr = explode( "/", $start_date );
                        
			$requestDate = mktime ( 0, 0, 0, $dateArr [0], $dateArr [1], $dateArr [2] );

			$jd = GregorianToJD ( $dateArr [0], $dateArr [1], $dateArr [2] ); //GregorianToJD(m, d, y);
		} else {
			$requestDate = mktime ( 0, 0, 0, date ( "m" ), date ( "d" ), date ( "Y" ) );
			$jd = GregorianToJD ( date ( "m" ), date ( "d" ), date ( "Y" ) ); //GregorianToJD(m, d, y);
		}
		
		$slotDays['day1'] = strtotime("+0 day",$requestDate);
		$slotDays['day2'] = strtotime("+1 day",$requestDate);
		$slotDays['day3'] = strtotime("+2 day",$requestDate);
		$slotDays['day4'] = strtotime("+3 day",$requestDate);
		$slotDays['day5'] = strtotime("+4 day",$requestDate);
		if($type) {
			$slotDays['day6'] = strtotime("+5 day",$requestDate);
			$slotDays['day7'] = strtotime("+6 day",$requestDate);
		}
		$test = date('Y-m-d H:i', $slotDays['day2']);


		$button_prev_date = "";
		$button_next_date = "";

		$requestDay = date ( "l", $requestDate );

		$weekFirstDate		=	$jd;
		$weekSecondDate		=	$jd+1;
		$weekThirdDate		=	$jd+2;
		$weekFourthDate		=	$jd+3;
		$weekFifthDate		=	$jd+4;
		$weekSixthDate		=	$jd+5;
		$weekSeventhDate	=	$jd+6;

		$settings = new Admin_Model_GlobalSettings();
		$dateFormat = $settings->settingValue('date_format');
		$hours = $settings->settingValue('hours');
		if($hours) {
			$timeFormat = "%I:%M %P";
		} else {
			$timeFormat = "%H:%M";
		}
		
		$day1 = strftime("%A",$slotDays['day1'])." <span>".date($dateFormat, strtotime(JDToGregorian($weekFirstDate)))."</span>";
		$day2 = strftime("%A",$slotDays['day2'])." <span>".date($dateFormat, strtotime(JDToGregorian($weekSecondDate)))."</span>";
		$day3 = strftime("%A",$slotDays['day3'])." <span>".date($dateFormat, strtotime(JDToGregorian($weekThirdDate)))."</span>";
		$day4 = strftime("%A",$slotDays['day4'])." <span>".date($dateFormat, strtotime(JDToGregorian($weekFourthDate)))."</span>";
		$day5 = strftime("%A",$slotDays['day5'])." <span>".date($dateFormat, strtotime(JDToGregorian($weekFifthDate)))."</span>";
		if($type) {
			$day6 = strftime("%A",$slotDays['day6'])." <span>".date($dateFormat, strtotime(JDToGregorian($weekSixthDate)))."</span>";
			$day7 = strftime("%A",$slotDays['day7'])." <span>".date($dateFormat, strtotime(JDToGregorian($weekSeventhDate)))."</span>";
		}


		$weekFirstDate = $jd;//sobiAxSearch::getWeekFirstDate ( $requestDay, $jd );
		$weekSeventhDate = $jd+6;//sobiAxSearch::getWeekFirstDate ( $requestDay, $jd + 6 );

		$startday = JDToGregorian ( $weekFirstDate );

		$arrStartday = explode( "/", $startday );

		if($type) {
			$button_prev_date = JDToGregorian($jd-7);
			$button_next_date = JDToGregorian($jd+7);
			$appointment_head ='
                            
				<li class="appoint-1">'.$day1.'</li>
				<li class="appoint-1">'.$day2.'</li>
				<li class="appoint-1">'.$day3.'</li>
				<li class="appoint-1">'.$day4.'</li>
				<li class="appoint-1">'.$day5.'</li>
				<li class="appoint-1">'.$day6.'</li>
				<li class="appoint-1">'.$day7.'</li>
			';
		} else {
			$button_prev_date = JDToGregorian($jd-5);
			$button_next_date = JDToGregorian($jd+5);
			$appointment_head ='
			<ul>
				<li><span>'.$day1.'</span></li>
				<li><span>'.$day2.'</span></li>
				<li><span>'.$day3.'</span></li>
				<li><span>'.$day4.'</span></li>
				<li><span>'.$day5.'</span></li>
			</ul>';
		}
		$requestDay = date("l", $requestDate);

		if($todayDate < $requestDate){
		} else {
			$button_prev_date = "";
		}
                
		$timestampStartday = mktime ( 0, 0, 0, $arrStartday [0], $arrStartday [1], $arrStartday [2] );
		$startdayDate = date ( "Y-m-d", $timestampStartday );

		$endday = JDToGregorian ( $weekSeventhDate );
		$arrEndday = explode( "/", $endday );
		$timestampEndday = mktime ( 0, 0, 0, $arrEndday [0], $arrEndday [1], $arrEndday [2] );
		$enddayDate = date ( "Y-m-d", $timestampEndday );
		$return_data = "";
		
		if($drid>0) {  
			$Slotmessage = new Application_Model_AppointmentSlotMessage();
			$messageObj = $Slotmessage->fetchRow("`doctor_id` = {$drid} AND `status`=1");
			if(!empty($messageObj)){
				// block doctor appointment slots with message.
				$return_data = "<div class=\"no-app-available\"><span>".$messageObj->getMessage()."</span></div>";
				$returnArray['return_data']         = $return_data;
				$returnArray['drid']                = $drid;
				$returnArray['appointment_head']    = $appointment_head;
				$returnArray['button_prev_date']    = $button_prev_date;
				$returnArray['button_next_date']    = $button_next_date;
				echo json_encode($returnArray);exit;
			}

			// Bronze members do not get appointments
			$Doctor = new Application_Model_Doctor();
			$objDoctor = $Doctor->find($drid);
			if($objDoctor){
				if(in_array($objDoctor->getMembershipLevel(),array("Bronze Premium", "Bronze"))){
						$return_data = "<div class=\"no-app-available\"><span>No appointment available.</span></div>";
				}
			}

			if($return_data==""){
				$availableSlotDays = array();

				$TimeSlot = new Base_Timeslot();
				$Appointment = new Application_Model_Appointment();
				$Holiday = new Application_Model_HolidayList();

				$maxLink = 8;
				$isNoSlot = 1;
				$return_data = "<ul>";
				foreach($slotDays as $perday){ // foreach for date
					$date = date('Y-m-d', $perday);
					$max = 0;
					$isMoreLink = false;
					$return_data .= "<li class='appoint-1'>";
					$holidayObject = $Holiday->fetchRow("date='{$date}'");
					if(!empty($holidayObject)){
						$return_data .= "&nbsp;</li>";
						continue;
					}
					$slotArray = array();

					$slotArray = $this->getAppointmentCalTimeSlots($drid, $date);
					if(empty($slotArray)){
					
						$isDeletedSlot = $this->isDeletedSlot($drid, date('Y-m-d', $perday));
						if($isDeletedSlot===false){ // if this date is not delete from backend
							$slotArray = $TimeSlot->getDoctorTimeSlots($drid, date('Y-m-d', $perday));
						}
					}
					
					if(!empty($slotArray)){
						foreach($slotArray as $slotBite){ // foreach for per time slot in the date
						
						$explodeTime = explode(':', $slotBite);
						$checkTime = mktime($explodeTime[0], $explodeTime[1], 0, date('m', $perday), date('d', $perday), date('Y', $perday));
						$time_before_rv = $settings->settingValue('time_before_rv');
						
						$hours = $settings->settingValue('hours');
						if($hours) {
							$timeFormat = "%I:%M %P";
						} else {
							$timeFormat = "%H:%M";
						}
						$offset_time = strtotime("+".$time_before_rv." hours");
						if($checkTime < $offset_time)continue;
						
						$timetoshow = strftime($timeFormat, strtotime($slotBite));
						$dbTime = date('H:i', strtotime($slotBite));
						
						$isNoSlot = 0;
						$query = "doctor_id='{$drid}' AND appointment_date='{$date}' AND appointment_time='{$dbTime}' AND deleted!=1";
						//error_log($query);
						$booked = $Appointment->fetchRow($query);
						if(empty($booked)){// if appointment not booked
							$max++;
							if($max < $maxLink){
								$return_data .= "<a class='slots' rel='nofollow' href='/appointment/index/drid/$drid/date/{$date}/time/{$dbTime}/'>{$timetoshow}</a><br />";
							}elseif($max == $maxLink && (isset($post['disp']) && $post['disp']!='fancy')){ // if request from fancybox then no "more..." link
								$isMoreLink = true;
								$return_data .=  "<a href='javascript:;' rel='more$drid-$date' class='moreLink' >more...</a><div style='display: none;' id='more$drid-$date'>";
								$return_data .=  "<a class='slots' rel='nofollow' href='/appointment/index/drid/$drid/date/$date/time/$dbTime/'>".$timetoshow."</a><br />";
							}else{
								$return_data .=  "<a class='slots'rel='nofollow' href='/appointment/index/drid/$drid/date/$date/time/$dbTime/'>".$timetoshow."</a><br />";
							}

						}
					}
					
					if($isMoreLink) $return_data .=  "</div>";
					}else{
						//$return_data .= "";
					}
					$return_data .= "&nbsp;</li>";
				}
				$return_data .= "</ul>";

				if($isNoSlot) $return_data = "<div class=\"no-app-available\"><span>No available timeslots this week</span></div>";
			}
		}
		$returnArray['testing']         = $test;
		$returnArray['return_data']         = $return_data;
		$returnArray['drid']                = $drid;
		$returnArray['appointment_head']    = $appointment_head;
		$returnArray['button_prev_date']    = $button_prev_date;
		$returnArray['button_next_date']    = $button_next_date;
		echo json_encode($returnArray);exit;
		//################## Generetae Appointment availability list ######################

		//echo $return_data."###".$drid."###".$appointment_head."###".$button_prev_date."###".$button_next_date."###";
	}// end function

    public function getSlot($appDate, $drid)
	{
		$arr = array();

        $Timeslot = new Application_Model_DoctorAppointmentCal();
        $slotObj = $Timeslot->fetchAll("doctor_id='{$drid}' AND slot_date = '{$appDate}'", "slot_time");
		
		if(!empty($slotObj)) {
			foreach($slotObj as $k => $obj) {
				$arr[] = date("H:i",strtotime($appDate." ".$obj->getSlotTime()));
			}
		}
		return $arr;
	}

        public function getDoctorTimeSlots($drid, $date, $slotFrmt=null){// if format is 1 then display time is h:i AM/PM else H:i
			
            $returnArray = array();
            $weekNumber = $this->fetchSlotWeek($date);
            $slotDay = strtoupper(date('D', strtotime($date)));
            //echo "doctor_id='$drid' AND week_number='{$weekNumber}' AND is_checked='1' AND slot_day='{$slotDay}' <br>";
            $MasterSlot = new Application_Model_MasterTimeslot();
            $object = $MasterSlot->fetchRow("doctor_id='$drid' AND week_number='{$weekNumber}' AND is_checked='1' AND slot_day='{$slotDay}'", "id ASC");
            if($object){
                    $returnArray = $this->breakTimeslots($object->getStartTime(), $object->getEndTime(), $object->getSlotInterval(), $slotFrmt, $object->getDisplaySlots());
					//error_log(print_r($returnArray, true));
            }else{
                $object1 = $MasterSlot->fetchRow("doctor_id='$drid'");
                if(!$object1){
                    $object = $MasterSlot->fetchRow("doctor_id='-1' AND week_number='{$weekNumber}' AND is_checked='1' AND slot_day='{$slotDay}'", "id ASC");
                    if($object)$returnArray = $this->breakTimeslots($object->getStartTime(), $object->getEndTime(), $object->getSlotInterval(), $slotFrmt);
                }
            }
            
            return $returnArray;
        }

        public function breakTimeslots($startTime, $endTime, $interval, $slotFrmt=null, $dispTime=null){
            $returnArray = array();
            if($startTime!='' && $endTime!='' && $interval > 0){
                
                for ($t = strtotime($startTime); $t <= strtotime($endTime); $t = ($t + (60 * $interval))) {
                    if($slotFrmt==1){
                        $timeSlot = strftime('%I:%M %P', $t);
                    }else{
                        $timeSlot = date('H:i', $t);
                    }
                    if(!empty($dispTime)){
                        $dispArray = explode(",", $dispTime);
                        //$checkSlot = strftime('%I:%M %P', $t);
						$checkSlot= date('H:i', $t);
                        if(in_array($checkSlot, $dispArray)){
                            $returnArray[$timeSlot] = $timeSlot;
                        }
                    }else{
                        $returnArray[$timeSlot] = $timeSlot;
                    }
                }
               
            }
            return $returnArray;
        }
        
        public function fetchSlotWeek($date){
            if($date=='')return 1;
            $array = array( '01'=>'1','02'=>'2',
                            '03'=>'1','04'=>'2',
                            '05'=>'1','06'=>'2',
                            '07'=>'1','08'=>'2',
                            '09'=>'1','10'=>'2',
                            '11'=>'1','12'=>'2',
                            '13'=>'1','14'=>'2',
                            '15'=>'1','16'=>'2',
                            '17'=>'1','18'=>'2',
                            '19'=>'1','20'=>'2',
                            '21'=>'1','22'=>'2',
                            '23'=>'1','24'=>'2',
                            '25'=>'1','26'=>'2',
                            '27'=>'1','28'=>'2',
                            '29'=>'1','30'=>'2',
                            '31'=>'1','32'=>'2',
                            '33'=>'1','34'=>'2',
                            '35'=>'1','36'=>'2',
                            '37'=>'1','38'=>'2',
                            '39'=>'1','40'=>'2',
                            '41'=>'1','42'=>'2',
                            '43'=>'1','44'=>'2',
                            '45'=>'1','46'=>'2',
                            '47'=>'1','48'=>'2',
                            '49'=>'1','50'=>'2',
                            '51'=>'1','52'=>'2'
                            );
            return $array[date('W', strtotime($date))];
            
        }

        public function getAppointmentCalTimeSlots($drid, $date){
            $returnArray = array();
            $DoctorAppointmentCal = new Application_Model_DoctorAppointmentCal();
            $object = $DoctorAppointmentCal->fetchAll("doctor_id='{$drid}' AND slot_date='{$date}'", "slot_time");
            if($object){
                foreach($object as $slot){
                    $returnArray[$slot->getSlotTime()] = $slot->getSlotTime();
                }
            }
            return $returnArray;
        }

        public function isDeletedSlot($drid, $date){
            
            $AppointmentRemovedDate = new Application_Model_AppointmentRemovedDate();
            $object = $AppointmentRemovedDate->fetchRow("doctor_id='{$drid}' AND slot_date='{$date}'");
            
            if($object)return true;

            return false;
        }

}// end class