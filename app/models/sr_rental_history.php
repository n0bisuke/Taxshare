<?php
class SrRentalHistory extends AppModel{
	var $name = 'SrRentalHistory';
	
	/*ログ記録用*/
	function inputLog($rental_id,$user_id,$comment,$type){
     	$this->data = array("SrRentalHistory" => array(//コントローラ側で$this->dataを指定
			"rental_id"	=> $rental_id,
			"user_id"	=> $user_id,
			"comment" => $comment,
			"type" => $type
		));
     	$this->save($this->data);
	}	
	
}
?>