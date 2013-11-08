<?php
class LgUser extends AppModel{
	var $name = 'LgUser';
	
	//facebookと上手く連携して、ユーザ登録、更新ができる関数
	public function facebook($facebook_data){
		$user_data = $this->find('first', array('conditions' => array('facebook_id' => $facebook_data['facebook_id'])));
		if($user_data){
			$facebook_data['id'] = $user_data['LgUser']['id'];
		}
		$result = $this->save(array("LgUser" => $facebook_data));
	}
	
	
	
/*
	//twitterと上手く連携して、ユーザ登録、更新ができる関数
	public function twitter($twitter_data){
		$user_data = $this->find('first', array('conditions' => array('twitter_id' => $twitter_data['twitter_id'])));
		if ($user_data) {//twitterアカウントが登録してある場合
			$twitter_data['id'] = $user_data['SrUser']['id'];
			$twitter_data['username'] = $user_data['SrUser']['username'];//既に利用しているユーザー名で登録 twitterのスクリーンネーム上書き阻止
		}
		$this->save(Array("User" => $twitter_data));
	}
	*/
	
	//ゆーざIdから詳細情報を取得
	public function info($id){
		return $this->find('first',array('conditions' => array('id' => $id)));
	}
	
	//ポイントアップ関数
	function pointup($user_id,$point){
		$pointup = $point+1;//ポイント追加 貸出登録
		$conditions = array('SrUser.id' => $user_id);//idが一致するもの
		$contents = array('SrUser.point' => "'{$pointup}'");//
		$this->updateAll($contents,$conditions);
	}

	//ポイントダウン関数
	function pointdwon($user_id,$point){
		$pointdwon = $point-1;//ポイント消費 レンタル
		$conditions = array('SrUser.id' => $user_id);//idが一致するもの
		$contents = array('SrUser.point' => "'{$pointdwon}'");//
		$this->updateAll($contents,$conditions);
	}
	
}
?>