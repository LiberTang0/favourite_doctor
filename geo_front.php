<script type="text/javascript">

//<![CDATA[

var map = null;
var prev_pin = null;
var geocoder;


function onLoad() {
	map = new GMap(document.getElementById("map"));
	map.addControl(new GSmallMapControl());
	map.addControl(new GMapTypeControl());
	
	savedGeo=document.getElementById('geocode').value;
	if(savedGeo==''){
		map.setCenter(new GLatLng(37.441944, -122.141944), 1);
	}else{
		var tempArray=new Array();
		tempArray=savedGeo.split(",");
		
		map.setCenter(new GLatLng(tempArray[0],tempArray[1]), 6);
	}
	geocoder = new GClientGeocoder();

	GEvent.addListener(map, 'click', function(overlay, point) { 
		if (prev_pin) { 
			map.removeOverlay(prev_pin); 
			prev_pin = null; 
		} 
		if (point) { 
			pin = new GMarker(point); 
			map.addOverlay(pin); 
			prev_pin = pin; 
			document.getElementById('geocode').value='';
			document.getElementById('geocode').value=point.y+','+point.x;
		} 
	});

}

window.onload=onLoad;

    //]]>
	
 function copyGeoCode(){
 	//field_county
	//document.getElementById('field_geocode').value=document.getElementById('lat').value+','+document.getElementById('lng').value;
	//alert('hi');
	
 }	
 //field_street
 //field_city
 //field_postcode
 
    function showLocation() {
      var address = document.getElementById('street').value+","+document.getElementById('city').value+","+document.getElementById('zipcode').value;
      
      geocoder.getLocations(address, addAddressToMap);
      
    }

    function addAddressToMap(response) {
      map.clearOverlays();
	  

      if (!response || response.Status.code != 200) {
        alert("Sorry, we were unable to geocode that address");
      } else {
       
		//  map.setCenter(response, 4); 
	    place = response.Placemark[0];
        point = new GLatLng(place.Point.coordinates[1],
                            place.Point.coordinates[0]);
        map.setCenter(point, 16); 
		marker = new GMarker(point);
        map.addOverlay(marker);
        marker.openInfoWindowHtml(place.address + '<br>' +
          '<b>Country code:</b> ' + place.AddressDetails.Country.CountryNameCode);
		$('#geocode').val(place.Point.coordinates[1]+','+place.Point.coordinates[0]);
	  
	  }
    }
 
    </script>


	
