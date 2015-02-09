<?php

class Base_PHPCalendar {
    public function initCalendar($calday = null) {
		
		$settings = new Admin_Model_GlobalSettings();
		$locale = $settings->settingValue('locale');
		setlocale(LC_ALL, $locale);
		
		date_default_timezone_set($settings->settingValue('timezone'));
        
        $Calendar = new Zend_Session_Namespace("calendar");
        
        if (!isset($Calendar->CALDAY)) {
            $Calendar->CALDAY = time();
        }

        if ($calday!=null) {
            $Calendar->CALDAY = $calday;
        }
        
?>
        <div class="left-cal-header1">
			<span class="f-left"></span> 
			<div class="f-right calendaricon">
				<a href="/user/index/appointment/tab/day">
					<img src="/images/admin-images/cal_day1_icon.png" />
				</a> 
				<a href="/user/index/appointment/tab/week">
					<img src="/images/admin-images/cal_day7_icon.png" />
				</a> 
				<a href="/user/index/appointment/tab/month">
					<img src="/images/admin-images/cal_day30_icon.png" />
				</a>
			</div> 
		</div>
        <div class="left-cal-header">
            <span style="float:left">
				<a href="javascript:" onclick="miniCalMove('<?php print strtotime('-1 year', $Calendar->CALDAY); ?>'); return false;"><<</a>&nbsp;
                <a href="javascript:;" onclick="miniCalMove('<?php print strtotime('-1 month', $Calendar->CALDAY); ?>'); return false;"><</a>
			</span>
            <span style="float:none"><?php print strftime('%B %Y', $Calendar->CALDAY); ?></span>
            <span style="float:right"><a href="javascript:;" onclick="miniCalMove('<?php print strtotime('+1 month', $Calendar->CALDAY); ?>'); return false;">></a>&nbsp;
                <a href="javascript:;" onclick="miniCalMove('<?php print strtotime('+1 year', $Calendar->CALDAY); ?>'); return false;">>></a>
			</span>
        </div>
        <div class="left-day-name">
    <?php
        $CALDAYDay = date('t', $Calendar->CALDAY);
        $start = $Calendar->CALDAY;
        $start = $start - ((date('j', $start) - 1) * 24 * 60 * 60);
        $end = $start + ($CALDAYDay * 24 * 60 * 60);
        $nextMonth = strtotime('+1 month', $Calendar->CALDAY);
        $prevtMonth = strtotime('-1 month', $Calendar->CALDAY);
        $firstWeekDay = date('N', $start);
    ?>
        <table width="100%" cellpadding="0" cellspacing="0" border="0" class="min-call-left" height="200">
            <tr>
                <th><?php $day = strftime("%a", strtotime("Sunday")); echo strtoupper(substr($day, 0, 1)); ?></th>
                <th><?php $day = strftime("%a", strtotime("Monday")); echo strtoupper(substr($day, 0, 1)); ?></th>
                <th><?php $day = strftime("%a", strtotime("Tuesday")); echo strtoupper(substr($day, 0, 1)); ?></th>
                <th><?php $day = strftime("%a", strtotime("Wednesday")); echo strtoupper(substr($day, 0, 1)); ?></th>
                <th><?php $day = strftime("%a", strtotime("Thursday")); echo strtoupper(substr($day, 0, 1)); ?></th>
                <th><?php $day = strftime("%a", strtotime("Friday")); echo strtoupper(substr($day, 0, 1)); ?></th>
                <th><?php $day = strftime("%a", strtotime("Saturday")); echo strtoupper(substr($day, 0, 1)); ?></th>
            </tr>
        <?php
        $counter = 0;
        $td = $CALDAYDay;
        $loopStart = 1 - $firstWeekDay;
        $loopEnd = $CALDAYDay + (7 - $firstWeekDay);
        for ($loopStart; $loopStart <= $loopEnd; $loopStart++) {
            if ($counter % 7 == 0) {
                if ($counter == 0) {
                    print "<tr>";
                } else {
                    print "</tr><tr>";
                }
            }
            if ($loopStart > 0 && $loopStart <= $CALDAYDay) {
                $cls = 'this-month';
                if ($loopStart == date('d', $Calendar->CALDAY)) {
                    $cls = 'CALDAY';
                }
                //print '<td class="'.$cls.'" valign="middle">'. $loopStart.'';
                $ts = mktime(0, 0, 0, date('m', $Calendar->CALDAY), $loopStart, date('Y', $Calendar->CALDAY));
                print '<td class="' . $cls . '" valign="middle">
			<a href="/user/index/appointment/tab/day/today/' . $ts . '">' . $loopStart . '</a>';
                print '</td>';
            } else {
                print "<td class='next-month'>&nbsp;</td>";
            }
            $counter++;
        }
        ?>
    </table>

    <div class="left-cal-footer">
       <!-- <span><a href="/user/index/appointment/tab/day">Day</a></span>
        <span><a href="/user/index/appointment/tab/week">Week</a></span>
        <span ><a href="/user/index/appointment/tab/month">Month</a></span>-->
    </div>

    </div>
    <?php
    }// end function

    public function getTime24Hrs($time)
    {
       return date("H:i:s",strtotime(date("Y-m-d")." ".$time));
    }

    public function getTime12Hrs($time)
    {
            return date("h:i A",strtotime(date("Y-m-d")." ".$time));
    }

    public function getApp($drApp, $day, $docObject, $TODAY){
    $html='';
    //print '<div class="day-content">'. $loopStart.'</div>';
            $i=1;
            foreach($drApp as $row){
                $aday = date('d', strtotime($row->getAppointmentDate()));
                    //echo $row->day." : ".$day."<br />";
                    if($aday==$day){
                            $class = 'class="monthly-unapproved" ';
//                            $class = 'class="monthly-selected" ';
                if($row->getApprove()===0)  		$class = 'class="monthly-selected" ';
                elseif($row->getApprove()==1)  	$class = 'class="monthly-approved" ';
                elseif($row->getApprove()==2)  	$class = 'class="monthly-unapproved" ';

                $todayStartSlot = ''; // declared by Developer

                            // $html.='<div class="day-content">'. $row->patient_name.'</div>';
                            $html.="<div><a $class href='/user/index/view-appointment/appid/{$row->getId()}/tab/month'><span>$i. ".$row->getFname()."</span></a></div>";
                            // $html.=$i.'. '. $row->patient_name.'<br>';
                             $i++;
                    }
            }
            return $html;

    }

}// end class