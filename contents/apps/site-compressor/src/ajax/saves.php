<?php
$response = array();
$saves = getData("", "site-compressor");
if( $saves ){
   foreach($saves as $save){
     $saveName = $save['name'];
     $response[$saveName] = json_decode($save['content'], true);
   }
}
echo json_encode($response);
?>
