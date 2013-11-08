<?php
class RmgUser extends AppModel{
	var $name = 'RmgUser';
	
	//facebookと上手く連携して、ユーザ登録、更新ができる関数
	public function facebook($facebook_data){
		$user_data = $this->find('first', array('conditions' => array('facebook_id' => $facebook_data['facebook_id'])));
		if($user_data){
			$facebook_data['id'] = $user_data['SrUser']['id'];
			$facebook_data['username'] = $user_data['SrUser']['username'];//既に利用しているユーザー名で登録 facebookのスクリーンネーム上書き阻止
			$facebook_data['email'] = $user_data['SrUser']['email'];//既に利用しているメールアドレスで登録 facebookで登録しているアドレスでの上書き阻止
		}
		$result = $this->save(array("User" => $facebook_data));
	}

	//twitterと上手く連携して、ユーザ登録、更新ができる関数
	public function twitter($twitter_data){
		$user_data = $this->find('first', array('conditions' => array('twitter_id' => $twitter_data['twitter_id'])));
		if ($user_data) {//twitterアカウントが登録してある場合
			$twitter_data['id'] = $user_data['RmgUser']['id']; //更新用
			$twitter_data['twitter_id'] = $user_data['RmgUser']['twitter_id'];//既に利用しているユーザー名で登録 twitterのスクリーンネーム上書き阻止
		}
		$this->save(Array("RmgUser" => $twitter_data));
	}
	
	
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