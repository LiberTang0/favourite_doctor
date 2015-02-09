<script type="text/javascript">

//<![CDATA[

var map = null;
var prev_pin = null;
var geocoder;
var marker;


function onLoad() {
	map = new google.maps.Map(document.getElementById("map"),{
		zoom: 13,
        mapTypeId: google.maps.MapTypeId.ROADMAP
	});
	
	savedGeo=document.getElementById('geocode').value;
	if(savedGeo==''){
		map.setCenter(new google.maps.LatLng(37.441944, -122.141944), 1);
	}else{
		var tempArray=new Array();
		tempArray=savedGeo.split(",");
		
		map.setCenter(new google.maps.LatLng(tempArray[0],tempArray[1]), 6);
	}
	geocoder = new google.maps.Geocoder();

	google.maps.event.addListener(map, 'click', function(overlay, point) { 
		if (prev_pin) { 
			map.removeOverlay(prev_pin); 
			prev_pin = null; 
		}
		if (point) { 
			pin = new google.maps.Marker(point, map); 
			prev_pin = pin; 
			document.getElementById('geocode').value='';
			document.getElementById('geocode').value=point.y+','+point.x;
		} 
	});
	//showLocation();
	showLocation1(map);

}

google.maps.event.addDomListener(window, 'load', onLoad);

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
      
      geocoder.geocode({ 'address':address}, addAddressToMap);
      
    }
	
	function showLocation1(map){
		langlong = document.getElementById('geocode').value;
		if(langlong != ''){
			langlong = langlong.split(",");
			point = new google.maps.LatLng(langlong[0], langlong[1]);
			map.setCenter(point, 16);
			marker = new google.maps.Marker({
				position: point, 
				map: map
			});
		}
	}

    function addAddressToMap(results, status) {
	  //map.clearOverlays();
	  if(marker){
		marker.setMap(null);
	  }

      if (status !== google.maps.GeocoderStatus.OK) {
        alert("Sorry, we were unable to geocode that address");
      } else {
       
		//  map.setCenter(response, 4); 
	    point = results[0].geometry.location;
       // point = new google.maps.LatLng(place.Point.coordinates[1],
        //                    place.Point.coordinates[0]);
        map.setCenter(point, 16); 
		marker = new google.maps.Marker({position: point, map: map});
        var infoWindow = new google.maps.InfoWindow();
		infoWindow.setContent(results[0].formatted_address + '<br>');
		infoWindow.open(map,marker);
		$('#geocode').val(point.lat()+','+point.lng());
		
	  
	  }
    }

    </script>


	
