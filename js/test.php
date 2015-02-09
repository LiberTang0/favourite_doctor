<?php
header('content-type:text/javascript');
?>
jQuery(document).ready(function(){
setTimeout(loadSlotData, 5000);
loadSlotData("<?php echo date('m/d/Y')?>", "p");
});