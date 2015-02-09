$(function(){

	function initialCheck(){
		if($("#errorMessage").html()) {
			$("#errorLauncher").fancybox({
					'width' : 200,
					'height' : 300,
					'autoScale' : true,
					'transitionIn' : 'none',
					'transitionOut' : 'none',
					'autoDimensionst' : false,
					'hideOnContentClick': true,
					'content': $("#errorMessage").html()
				});
			$("#errorLauncher").click();
		}
	}
	initialCheck();
	// function for appending step visualization
	function addVisualization(id){
		if(id=='step1') {
			
		} else if(id=='your_info') {	
			
		} else if(id=='step2form') {
			
		} else if(id=='smsForm') {
		
		} else if(id=='finalstep') {
			jQuery('#appointment_done').show();
			jQuery('#appointment_title').hide();
			jQuery('#final_step_title').show();
			
			if(jQuery('#status_val').val() == 'e')
				jQuery('#logOutLink').show();
			else
				jQuery('#regOutLink').show();
		}
	}
	
	//dress up inputs
	$('form.jqtransform').jqTransform();
	//hide all future steps
	$('#did_insurance, #your_info, #login, #step2form, #smsForm, #register, #finalstep, .loading').css('display', 'none');
	
/*---------------------------- step1 ---------------------------------*/	
	
	//step1 - show initial fields
	$(".f-right").fancybox({
		'width' : 500,
		'height' : 600,
		'autoScale' : true,
		'transitionIn' : 'none',
		'transitionOut' : 'none',
		'autoDimensionst' : false
	});

	//step1 - show reasons field
	jQuery("#step1first .jqTransformSelectWrapper ul li a").click(function(){
		if(jQuery(this).html() == 'other'){
			jQuery('#other_reason').removeClass('wh-heading').addClass('wh-heading-dwn');
			jQuery('#other_reason').slideDown(500);
		}else{
			jQuery('#other_reason').removeClass('wh-heading-dwn').addClass('wh-heading');
			jQuery('#other_reason').slideUp(500);
		}
	});
	
	//step1 - show insurances field (needs)
	jQuery(".jqTransformRadioWrapper a").click(function(){
		if(jQuery(this).next().val() == 2){
			jQuery('#did_insurance').removeClass('wh-heading').addClass('wh-heading-dwn');
			jQuery('#did_insurance').slideDown(500);
		}
		else{
			jQuery('#did_insurance').removeClass('wh-heading-dwn').addClass('wh-heading');
			jQuery('#did_insurance').slideUp(500);
		}
	});
	
	
	/* step1 - validate */
	function validateStep1(){
		error_flag = 0;
		var drid_val = $("#drid").val();
		var appointment_time_val = $("#appointment_time").val();
		var appointment_date_val = $("#appointment_date").val();
		var reason_val = $("#reason_to_visit").val();
		var is_paying = get_radio_value('paying');
		var insurance = $("#insurance_company").val();
		var needs_val = $("#needs").val();
		var error_msg = "<div id='errorbox'><h4>Please fill in the following fields:</h4><ul id='errorList'>";
		if(appointment_time_val=="" ) { 
			error_msg += "<li>Appointmetn time</li>";	
			error_flag = 1; 
		}
		if(appointment_date_val=="") { 
			error_msg += "<li>Appointment date</li>";	
			error_flag = 1; 
		}
		if(reason_val=="" || (reason_val=="0" && needs_val=="") || (reason_val=="0" && needs_val=="other") ){ 
			error_msg += "<li>Reason of visit</li>";	
			error_flag = 1; 
		}
		if(is_paying=="" || is_paying==null) { 
			error_msg += "<li>Insurance</li>";		
			error_flag = 1; 
		}
		if(is_paying==2 && insurance<1) { 
			error_msg += "<li>Insurance</li>";		
			error_flag = 1; 
		}
		error_msg +="</ul></div>";
		if(error_flag) { 
			//alert(error_msg); 
			$("#errorMessage").html(""); //clean any previous messages
			$("#errorMessage").html(error_msg);
			$("#errorLauncher").fancybox({
				'width' : 200,
				'height' : 300,
				'autoScale' : true,
				'transitionIn' : 'none',
				'transitionOut' : 'none',
				'autoDimensionst' : false,
				'hideOnContentClick': true,
				'content': error_msg
			});
			$("#errorLauncher").click();

			return false; 
		} else {
			return true;
		}
	}

/*---------------------------- step2 ---------------------------------*/

	/* step2 - procceed to 2nd step your_info */
	$("#nxtStp1").click(function (){
		//check if all fields are ok
		if(validateStep1()) {
			$.post('/appointment/check-login/', function(bool){ 
				if(bool==1){ //if already logged in
					jQuery('#step1').fadeOut(500,function(){});
					getPatientdetails(); //fill content
					$('#status_val').val('e');	//status - logged in as an old user
					
					$("#appoint_form").formwizard("show","step2form");
					addVisualization("step2form");
					setFormVisuals();
					return true;
				} else { // not logged in, show login/register buttons
					addVisualization("your_info");
					$("#appoint_form").formwizard("show","your_info");
					return false;
				}
			});
		}
	}); 
	
/*---------------------------- step2 - login ---------------------------------*/	
	//step2 - show login form
	jQuery('#loginbtn').click(function(){		
		jQuery('#your_info').hide();
		jQuery('#login').show();
		jQuery('#status_val').val('e');
		addVisualization("login");
		$("#appoint_form").formwizard("show","login");
	});	
	//step2 - validate login form, proceed to step2form
	$("#log2step2btn").click(function (){		
		var error_flag = 0;
		var username = $("#username").val();
		var password = $("#password").val();
		var rememberMe = 0;
		if($("#rememberMe").attr('checked')){
			rememberMe = 1;
		}
		var error_msg = "<div id='errorbox'><h4>Please fill in the following fields:</h4><ul id='errorList'>";
		if(username=="" ) { 
			error_msg += "<li>Email</li>";	
			error_flag = 1; 
		}
		if(!isEmail(username) && username != '') { 
			error_msg += "<li>A valid Email</li>";	
			error_flag = 1; 
		}
		if(password=="") { 
			error_msg += "<li>Password</li>";	
			error_flag = 1; 
		}
		error_msg +="</ul></div>";
		
		if(error_flag) { 
			//alert(error_msg); 
			$("#errorMessage").html(""); //clean any previous messages
			$("#errorMessage").html(error_msg);
			$("#errorLauncher").fancybox({
				'width' : 200,
				'height' : 300,
				'autoScale' : true,
				'transitionIn' : 'none',
				'transitionOut' : 'none',
				'autoDimensionst' : false,
				'hideOnContentClick': true,
				'content': error_msg
			});
			$("#errorLauncher").click();
			return false; 
		}
		$.post('/appointment/do-login/', {
			username:username,
			password:password,
			rememberMe:0
		},
		function(html){
			var decoded = $.json.decode(html);
			if(decoded['err']=='1'){
				var error_msg = "<div id='errorbox'><h4>There was an error</h4><ul id='errorList'>";
				error_msg += decoded['msg'];
				error_msg +="</ul><div>";
				$("#errorMessage").html(""); //clean any previous messages
				$("#errorMessage").html(error_msg);
				$("#errorLauncher").fancybox({
					'width' : 200,
					'height' : 300,
					'autoScale' : true,
					'transitionIn' : 'none',
					'transitionOut' : 'none',
					'autoDimensionst' : false,
					'hideOnContentClick': true,
					'content': error_msg
				});
				$("#errorLauncher").click();
				//alert(error_msg);
			} else {
				getPatientdetails(); //fill content
				$('#status_val').val('e');	//status - logged in as an old user
				$("#appoint_form").formwizard("show","step2form");
				addVisualization("step2form");
			}
		});			
	});
	
	
	
/*---------------------------- step2 - register ---------------------------------*/	
	//step2 - show register form	
	jQuery('#registerbtn').click(function(){	
		$("#newReg").val("true");
		jQuery('#your_info').hide();
		jQuery('#status_val').val('n');
		$("#appoint_form").formwizard("show","register");
	});
	
	//step2 - validate registration form, proceed to step2form	
	$("#reg2step2btn").click(function (){
		var error_flag = 0;
		var username = $("#newemail").val();
		var password = $("#newpassword").val();
		var terms = 0;
		
		var error_msg = "<div id='errorbox'><h4>Please fill in the following fields:</h4><ul id='errorList'>";
		
		if($("#term").attr('checked')){
			error_flag = 0; 
		} else {
			error_flag = 1; 
			error_msg += "<li>You must aggree to the terms and conditions</li>";
		}
		
		if(username=="" ) { 
			error_msg += "<li>Email</li>";	
			error_flag = 1; 
		}
		if(!isEmail(username) && username != '') { 
			error_msg += "<li>A valid Email</li>";	
			error_flag = 1; 
		}
		if(password=="") { 
			error_msg += "<li>Password</li>";	
			error_flag = 1; 
		}
		
		error_msg +="</ul></div>";
		
		if(error_flag) { 
			//alert(error_msg); 
			$("#errorMessage").html(""); //clean any previous messages
			$("#errorMessage").html(error_msg);
			$("#errorLauncher").fancybox({
				'width' : 200,
				'height' : 300,
				'autoScale' : true,
				'transitionIn' : 'none',
				'transitionOut' : 'none',
				'autoDimensionst' : false,
				'hideOnContentClick': true,
				'content': error_msg
			});
			$("#errorLauncher").click();
			return false;
		} else {
	
	
			var newemail = jQuery.trim($("#newemail").val());
			$.post('/appointment/checknewmail/', {
				newemail:newemail
			},
			function(html){
				var decoded = $.json.decode(html);
				if(decoded['err']=='1'){
					var error_msg = "<div id='errorbox'><h4>There was an error</h4><ul id='errorList'>";
					error_msg += decoded['msg'];
					error_msg +="</ul><div>";
					$("#errorMessage").html(""); //clean any previous messages
					$("#errorMessage").html(error_msg);
					$("#errorLauncher").fancybox({
						'width' : 200,
						'height' : 300,
						'autoScale' : true,
						'transitionIn' : 'none',
						'transitionOut' : 'none',
						'autoDimensionst' : false,
						'hideOnContentClick': true,
						'content': error_msg
					});
					$("#errorLauncher").click();
				} else {
					$("#appoint_form").formwizard("show","step2form");
					addVisualization("step2form");
				}
			});	
		}
	});
/*---------------------------- step2 - step2form (patient info) ---------------------------------*/	
	
	//step2 - validate step2form, proceed to sms
	$("#smsFormbtn").click(function (){
		//validate data form
		if(checkAppointmentForm()) {
			if($("#smsPlugin").html()==0) {
				addVisualization("sureForm");
				$("#appoint_form").formwizard("show","sureForm");
			} else {
				addVisualization("smsForm");
				$("#appoint_form").formwizard("show","smsForm");
			}
		}
	});
	
/*---------------------------- step3 - sms ---------------------------------*/		

	//step3 - show extra field smscode and send phone to action for code production
	jQuery('#smsphonebtn').click(function(){
		jQuery(".attention").hide();
		jQuery(".loading").show();
		
		$.post('/appointment/smsphone/', {
			smsphone:$('#smsphone').val()
		},
		function(html){
			var decoded = $.json.decode(html);
			if(decoded['err'] == 1){
				var error_msg = "<div id='errorbox'><h4>Wrong Password</h4><ul id='errorList'>";
				error_msg += decoded['msg'];
				error_msg +="</ul><div>";
				$("#errorMessage").html(""); //clean any previous messages
				$("#errorMessage").html(error_msg);
				$("#errorLauncher").fancybox({
					'width' : 200,
					'height' : 300,
					'autoScale' : true,
					'transitionIn' : 'none',
					'transitionOut' : 'none',
					'autoDimensionst' : false,
					'hideOnContentClick': true,
					'content': error_msg
				});
				$("#errorLauncher").click();
				return false;
			}
			jQuery(".loading").hide();
			jQuery('#smsCodeWrapper').show();
		});
	});
	
	//step3 - validate sms code and procceed to final check	
	$("#endbtn").click(function (){
		$.post('/appointment/smscode/', {
			smsphone:$('#smsphone').val(),
			smscode:$('#smscode').val()
		},
		function(html){
			var decoded = $.json.decode(html);
			if(decoded['err']=='1'){
				var error_msg = "<div id='errorbox'><h4>Wrong Password</h4><ul id='errorList'>";
				error_msg += decoded['msg'];
				error_msg +="</ul><div>";
				$("#errorMessage").html(""); //clean any previous messages
				$("#errorMessage").html(error_msg);
				$("#errorLauncher").fancybox({
					'width' : 200,
					'height' : 300,
					'autoScale' : true,
					'transitionIn' : 'none',
					'transitionOut' : 'none',
					'autoDimensionst' : false,
					'hideOnContentClick': true,
					'content': error_msg
				});
				$("#errorLauncher").click();
				//alert(error_msg);
			} else {
				addVisualization("sureForm");
				$("#appoint_form").formwizard("show","sureForm");
			}
		});
	});

/*---------------------------- step4 - final ---------------------------------*/			
		
		$("#acceptSubmit").click(function (){
			submitAppointment();
			console.log("appointment ok");
			if(jQuery('#status_val').val == 'n') { //just registered
				$.post('/appointment/register-patient/', {
					newemail:newemail,
					newpassword:newpassword,
					app_id:$('#app_id').val()
				},
				function(html){
					var decoded = $.json.decode(html);
					if(decoded['err']=='1'){
						var error_msg = "<div id='errorbox'><h4>There was an error</h4><ul id='errorList'>";
						error_msg += decoded['msg'];
						error_msg +="</ul><div>";
						$("#errorMessage").html(""); //clean any previous messages
						$("#errorMessage").html(error_msg);
						$("#errorLauncher").fancybox({
							'width' : 200,
							'height' : 300,
							'autoScale' : true,
							'transitionIn' : 'none',
							'transitionOut' : 'none',
							'autoDimensionst' : false,
							'hideOnContentClick': true,
							'content': error_msg
						});
						$("#errorLauncher").click();
						return false;
					} 
				});
			}
		});
		
		$("#notAcceptSubmit").click(function () {
			document.location = "/";
		});
	
/*---------------------------- support functions ---------------------------------*/	

	//take all the patient's data
	function getPatientdetails(){
		$.post('/appointment/get-patient-details/', {},
		function(html){
			var decoded = $.json.decode(html);
			if(decoded['email']!=''){
				fill_patient_info(decoded);
				jQuery('#your_info').fadeOut(500,function(){
					jQuery('#crumbA2').addClass('active');
					jQuery('#step2form').fadeIn(500);
				});
			}else{
				jQuery('#your_info').fadeOut(500,function(){
					jQuery('#loginForm').fadeIn(500);
				});
			}
		});
	}
	
	//validate substep2 registration
	function validateRegistration(){
		var error_flag = true;
		var newemail = jQuery.trim($("#newemail").val());
		var newpassword = jQuery.trim($("#newpassword").val());
		var error_msg = "<div id='errorbox'><h4>Please fill in the following fields:</h4><ul id='errorList'>";
		if(newemail=="" ) {
			error_msg += "<li>Email</li>";	
			error_flag = false; 
		}
		if(!isEmail(newemail) && newemail != '') { 
			error_msg += "<li>A Valid Email</li>";	
			error_flag = false; 
		}
		if(newpassword=="") { 
			error_msg += "<li>Password</li>";	
			error_flag = false; 
		}
		if(newpassword!='' && newpassword.length < 6) {
			error_msg += "<li>Password must be at least 6 characters long</li>";	
			error_flag = false; 
		}
		if($("#term").attr('checked')==false) { 
			error_msg += "<li>You must agree with terms and conditions</li>";	
			error_flag = false; 
		}
		error_msg +="</ul></div>";
		if(!error_flag) {
			//alert(error_msg);
			$("#errorMessage").html(""); //clean any previous messages
			$("#errorMessage").html(error_msg);
			$("#errorLauncher").fancybox({
				'width' : 200,
				'height' : 300,
				'autoScale' : true,
				'transitionIn' : 'none',
				'transitionOut' : 'none',
				'autoDimensionst' : false,
				'hideOnContentClick': true,
				'content': error_msg
			});
			$("#errorLauncher").click();
		}	
		return error_flag;
			
	}
		
	//put all patient data to the form
	function fill_patient_info(options){
		$("#name").val(options['name'])
		$("#lastname").val(options['lastname'])
		$("#zipcode").val(options['zipcode'])
		$("#phone").val(options['phone'])
		$("#email").val(options['email'])
		$("#month").val(options['month'])
		$("#day").val(options['day'])
		$("#year").val(options['year'])
		var gender = options['gender'];
		jQuery("input[name='gender']").each(function() {
			if(this.value==gender)
				this.checked=true;
		});
		
		setFormVisuals();
	}		
	
	function checkAppointmentForm() {
		var drid_val = $("#drid").val()
		var name_val = $("#name").val()
		var zipcode_val = $("#zipcode").val()
		var phone_val = $("#phone").val()
		var email_val = $("#email").val()
		var month = $("#month").val()
		var day = $("#day").val()
		var year = $("#year").val()
		var gender_val = get_radio_value('gender');
		var status_val = $("#status_val").val()
		var first_visit = get_radio_value('first_visit');
		var appointment_time_val = $("#appointment_time").val()
		var needs_val = $("#needs").val()
		var appointment_date_val = $("#appointment_date").val()
		var reason_val = $("#reason_to_visit").val()
		var insurance_company = $("#insurance_company").val()
		var insurance_plan = $("#insurance_plan").val()

		var error_flag = 0;
		var filter=/^.+@.+\..{2,3}$/
	
		var phone_filter =  /^[0-9-()]+$/;


		var error_msg = "<div id='errorbox'><h4>Please fill in the following fields:</h4><ul id='errorList'>";
		if(appointment_time_val=="" ) {
			error_msg += "<li>Appointment time</li>";	
			error_flag = 1; 
		}
		if(appointment_date_val=="") {
			error_msg += "<li>Appointment date</li>";	
			error_flag = 1; 
		}
		if(name_val=="") { 
			error_msg += "<li>Name</li>";				
			error_flag = 1; 
		}
		if(email_val=="") { 
			error_msg += "<li>Email</li>";			
			error_flag = 1; 
		} else if(!filter.test(email_val)) { 
			error_msg += "<li>A valid Email</li>"; 
			error_flag = 1; 
		}
		if(phone_val=="" || !phone_filter.test(phone_val)) {
			error_msg += "<li>A valid telephone number</li>";			
			error_flag = 1; 
		}
		if(month=="") { 
			error_msg += "<li>Date of birth</li>";			
			error_flag = 1; 
		}
		if(day=="")	{ 
			error_msg += "<li>Day of birth</li>";			
			error_flag = 1; 
		}
		if(year=="") { 
			error_msg += "<li>Year of birth</li>";			
			error_flag = 1; 
		}
		if(zipcode_val=="" || isNaN(zipcode_val)) { 
			error_msg += "<li>A valid ZIPcode</li>";			
			error_flag = 1; 
		}
		if(gender_val==""  || gender_val==null)	{ 
			error_msg += "<li>Gender</li>";			
			error_flag = 1; 
		}
		if(reason_val=="" || (reason_val=="0" && needs_val=="") || (reason_val=="0" && needs_val=="other")) { 
			error_msg += "<li>Reason of visit</li>";	
			error_flag = 1; 
		}
		error_msg +="</ul></div>";
		if(error_flag) { 
			//alert(error_msg);
			$("#errorMessage").html(""); //clean any previous messages
			$("#errorMessage").html(error_msg);
			$("#errorLauncher").fancybox({
				'width' : 200,
				'height' : 300,
				'autoScale' : true,
				'transitionIn' : 'none',
				'transitionOut' : 'none',
				'autoDimensionst' : false,
				'hideOnContentClick': true,
				'content': error_msg
			});
			$("#errorLauncher").click();				
			return false; 
		}
		if(first_visit)
			first_visit=1;
		else 
			first_visit=0;
		
		return true;
	}
		
	//final submit of the entire form.
	function submitAppointment() {
		var newemail = $("#newemail").val();
		var newpassword = $("#newpassword").val();
	
		var drid_val = $("#drid").val()
		var name_val = $("#name").val()
		var lastname_val = $("#lastname").val()
		var zipcode_val = $("#zipcode").val()
		var phone_val = $("#phone").val()
		var email_val = $("#email").val()
		var month = $("#month").val()
		var day = $("#day").val()
		var year = $("#year").val()
		var gender_val = get_radio_value('gender');
		var status_val = $("#status_val").val()
		var first_visit = get_radio_value('first_visit');
		var appointment_time_val = $("#appointment_time").val()

		var needs_val = $("#needs").val()
		var appointment_date_val = $("#appointment_date").val()
		var reason_val = $("#reason_to_visit").val()
		var insurance_company = $("#insurance_company").val()
		var insurance_plan = $("#insurance_plan").val()

		var error_flag = 0;
	
		$.post('/appointment/create-appointment/', {
			newemail:newemail, newpassword:newpassword, drid:drid_val, name:name_val, lastname:lastname_val, zipcode:zipcode_val,phone:phone_val,email:email_val,
			month:month,day:day,year:year,gender:gender_val,status:status_val,
			appointment_time:appointment_time_val,appointment_date:appointment_date_val,needs:needs_val,
			reason:reason_val,insurance_company:insurance_company,insurance_plan:insurance_plan,first_visit:first_visit
		},
		function(html){
			jQuery('#appointmentbtn2').removeClass('app-disabled');
			jQuery('#appointmentbtn2').addClass('app-submit');
			var decoded = $.json.decode(html);
			afterAppointment(decoded);
		});
	}
	
	function afterAppointment(decoded){		
		if(decoded['err']==1){
			var error_msg = "<div id='errorbox'><h4>Please fill in the following fields:</h4><ul id='errorList'>";
			error_msg += decoded['msg'];
			error_msg +="</ul></div>";
			//alert(error_msg);
			$("#errorMessage").html(""); //clean any previous messages
			$("#errorMessage").html(error_msg);
			$("#errorLauncher").fancybox({
				'width' : 200,
				'height' : 300,
				'autoScale' : true,
				'transitionIn' : 'none',
				'transitionOut' : 'none',
				'autoDimensionst' : false,
				'hideOnContentClick': true,
				'content': error_msg
			});
			$("#errorLauncher").click();	
			jQuery('#appointmentbtn2').attr('disabled',false);
		}else{
			if(decoded['email']){
				var nameData = decoded['name'];
				var lastnameData = decoded['lastname'];
				jQuery('#newemail').val(decoded['email']);
				jQuery('#app_id').val(decoded['app_id']);
				jQuery("#nameData").html(nameData);
				jQuery("#lastnameData").html(lastnameData);
				jQuery("#phoneData").html(decoded['phoneData']);
				jQuery("#birthData").html(decoded['birthData']);
				jQuery("#zipData").html(decoded['zipData']);
				if(decoded['genderData'] == "m") 
					jQuery("#genderData").html("man");
				else 
					jQuery("#genderData").html("woman");
				jQuery("#emailData").html(decoded['emailData']);
				addVisualization("finalstep");
				$("#appoint_form").formwizard("show","finalstep");
			}else{
				var nameData = decoded['name'];
				jQuery("#nameData").html(nameData);
				jQuery("#phoneData").html(decoded['phoneData']);
				jQuery("#birthData").html(decoded['birthData']);
				jQuery("#zipData").html(decoded['zipData']);
				if(decoded['genderData'] == "m") 
					jQuery("#genderData").html("man");
				else 
					jQuery("#genderData").html("woman");
				jQuery("#emailData").html(decoded['emailData']);
				addVisualization("finalstep");
				$("#appoint_form").formwizard("show","finalstep");
			}
			//window.location.replace("/appointment/thankyou/appid/"+decoded['app_id']);


		}
	}

/*---------------------------- general helping functions ---------------------------------*/		
		
		//make timeline links work
		jQuery("#crumbA1").click(function() {
			var id=$("#appoint_form").formwizard("state").currentStep; 
			if(id=='your_info')
				$("#appoint_form").formwizard("back");			
			return false;
		});
		jQuery("#crumbA2").click(function() {
			var id=$("#appoint_form").formwizard("state").currentStep; 
			if(id=='smsForm')
				$("#appoint_form").formwizard("back");
			return false;
		});
		jQuery("#crumbA3").click(function() {
			return false;
		});
		jQuery("#crumbA4").click(function() {
			return false;
		});
	
		//steps mechanism
		$("#appoint_form").formwizard({ 
			formPluginEnabled: true,
			validationEnabled: true,
			focusFirstInput : true
		}); 		
		
		// initial call to addVisualization (for visualizing the first step)
		addVisualization($("#appoint_form").formwizard("state").firstStep);

		// bind a callback to the step_shown event
		$("#appoint_form").bind("step_shown", function(event, data){
			$("#step_visualization").html(""); 
			
		});
		
		
		function setFormVisuals() {
			var my_day = $("#day").val();
			var myYear = $("#year").val();
			var myMonth = $("#month").val();
			

			if(jQuery("input[name='gender']").eq(0).attr('checked') == true) {
				jQuery('#gender1').prev().addClass('jqTransformChecked');
			}
			else if(jQuery("input[name='gender']").eq(1).attr('checked') == true) {
				jQuery('#gender2').prev().addClass('jqTransformChecked');
			}
			var index_month = jQuery('#the_month div ul li').eq(myMonth).text();
			
			if(my_day == "") {
				my_day = "Day";
			}
			jQuery('#the_day div div span').html(my_day);
			
			if(myMonth == "") {
				myMonth = "Month";
			}
			jQuery('#the_month div div span').html(index_month);
			if(myYear == "") {
				myYear = "Year";
			}
			jQuery('#the_year div div span').html(myYear);
		}
	});

/*---------------------------- the end ---------------------------------*/		