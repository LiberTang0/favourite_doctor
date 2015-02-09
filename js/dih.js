function isEmail(emailStr)
{
    var reEmail=/^[0-9a-zA-Z_\.-]+\@[0-9a-zA-Z_\.-]+\.[0-9a-zA-Z_\.-]+$/;
    if(!reEmail.test(emailStr))
    {
        return false;
    }
    return true;
}
function get_radio_value(element){
    var ret = false;
    jQuery("input[name='"+element+"']").each(function() {
        if(this.checked==true)ret = this.value;
    });
    return ret;
}



function JSwap(fbox, tbox) {
    var inarray = getSelectValues(tbox);
    jQuery('#'+fbox+' option').each(function(){
        if(this.selected && this.value != "" && (jQuery.inArray( this.value, inarray )<0)){
            jQuery('#'+tbox).append('<option value="'+this.value+'">'+this.innerHTML+'</option>');// add to destination select box
            jQuery('#'+fbox+' option[value="'+this.value+'"]').remove();// remove from source select box
        }
    })
}

function getSelectValues(id) {
    var returnArray = new Array();
    var i= 0;
    jQuery('#'+id+' option').each(function(){
        returnArray[i] = this.value;
        i++
    })
    return returnArray;
}

function JSadd(fbox, tbox) {
    var inarray = getSelectValues(tbox);
    jQuery('#'+fbox+' option').each(function(){
        if(this.selected && this.value != "" && (jQuery.inArray( this.value, inarray )<0)){
            jQuery('#'+tbox).append('<option label="'+jQuery(this).attr('label')+'" value="'+this.value+'">'+this.innerHTML+'</option>');// add to destination select box
        }
    })
}
function JSremove(fbox) {
    jQuery('#'+fbox+' option').each(function(){
        if(this.selected){
            jQuery('#'+fbox+' option[value="'+this.value+'"]').remove();// remove from source select box
        }
    })
}

function copy(fbox, tbox) {
    	var arrFbox = new Array();
	var arrTbox = new Array();
	var arrLookup = new Array();
	var arrTo = new Array();
	var i;
	for(i=0; i<tbox.options.length; i++) {

		arrLookup[tbox.options[i].text] = tbox.options[i].value;
		arrTbox[i] = tbox.options[i].text;
		arrTo[i] = tbox.options[i].value;
	}
        
	var fLength = 0;
	var tLength = arrTbox.length;

	for(i=0; i<fbox.options.length; i++) {
		arrLookup[fbox.options[i].text] = fbox.options[i].value;
		if(fbox.options[i].selected && fbox.options[i].value != "") {

			arrTbox[tLength] = fbox.options[i].text;
                     
                        //fbox.remove(i);//Code added by deve


			tLength++;
		} else {
			arrFbox[fLength] = fbox.options[i].text;
                        fLength++;
                        
		}
	}

	tbox.length = 0;
	var c;

	for(c=0; c<arrTbox.length; c++) {
		var no = new Option();
		no.value = arrLookup[arrTbox[c]];
		no.text = arrTbox[c];
		tbox[c] = no;
	}

        
}



function uncopy(fbox, tbox) {
	var arrFbox = new Array();
	var arrTbox = new Array();
	var arrLookup = new Array();
	var i;
	for(i=0; i<tbox.options.length; i++) {
		arrLookup[tbox.options[i].text] = tbox.options[i].value;
		arrTbox[i] = tbox.options[i].text;
	}
	var fLength = 0;
	var tLength = arrTbox.length
	for(i=0; i<fbox.options.length; i++) {
		arrLookup[fbox.options[i].text] = fbox.options[i].value;
		if(fbox.options[i].selected && fbox.options[i].value != ""){
			arrTbox[tLength] = fbox.options[i].text;
                        //Added by deve to add new option in from box
                         /*var elOptNew = document.createElement('option');
                         elOptNew.text = fbox.options[i].text;
                         elOptNew.value = fbox.options[i].value;
                         try {
                        tbox.add(elOptNew, null); // standards compliant; doesn't work in IE
                        
                      }
                      catch(ex) {
                        tbox.add(elOptNew); // IE only
                      }*/

                         

                        // Till here  added by deve
			tLength++;
		}else{
			arrFbox[fLength] = fbox.options[i].text;
			fLength++;
		}
	}

	fbox.length = 0;

	var c;
	for(c=0; c<arrFbox.length; c++) {
		var no = new Option();
		no.value = arrLookup[arrFbox[c]];
		no.text = arrFbox[c];
		fbox[c] = no;
	}

}

function selectAll(box){

	for(var i=0; i<box.length; i++){
		box.options[i].selected = true;
	}

}


function submitdoctoredit_admin()
{

    selectAll(document.getElementById('doctor_affiliation'));
    selectAll(document.getElementById('doctor_award'));
    selectAll(document.getElementById('doctor_association'));
    selectAll(document.getElementById('doctor_reason_for_visit'));
    selectAll(document.getElementById('doctor_insurance'));
    selectAll(document.getElementById('doctor_plan'));
    selectAll(document.getElementById('category_id'));
    selectAll(document.getElementById('extra_category_id'));


}


function addslashes( str ) {
    // Escapes single quote, double quotes and backslash characters in a string with backslashes
    //
    // version: 810.114
   
    // *     example 1: addslashes("kevin's birthday");
    // *     returns 1: 'kevin\'s birthday'

    return (str+'').replace(/([\\"'])/g, "\\$1").replace(/\0/g, "\\0");
}
