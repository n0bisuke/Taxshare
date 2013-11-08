<?php 
/*読み込み*/
App::import('Vendor', 'oauth', array('file' => 'OAuth'.DS.'oauth_consumer.php'));//twitter認証
App::import('Vendor', 'upgrade');//json_decode()を読み込み、twitter_callbackで利用
App::import('Vendor', 'facebook',array('file' => 'facebook'.DS.'src'.DS.'facebook_m.php'));//facebook認証

class TaxshareMController extends AppController {
	public $name = 'TaxshareM';
	public $uses = array('TsUser','TsSchedule','TsVenue','TsContact');
	public $components = array('Auth');
	public $layout = 'taxshare-mobile';
	public function authredirect(){ //[F1] ログインしている場合に強制的にmainに移動させる． 
			$user = $this->Auth->user();
			if(!empty($user)){
				$this->redirect('main');
			}
	}
	
	function beforeFilter(){//login処理の設定
	 	$this->Auth->allow('start','mobile','top','facebook','callback');//ログインしないで、アクセスできるアクションを登録する
	 	$this->Auth->loginRedirect = array('controller' => 'taxshare_m','action' => 'main');
    	$this->Auth->logoutRedirect = array('controller' => 'taxshare_m','action' => 'mobile');
    	$this->Auth->loginAction = array('controller' => 'taxshare_m','action' => 'mobile');
	 	//facebookでのログインテーブルとフィールド設定
	 	$this->Auth->userModel = 'TsUser';
		$this->Auth->fields = array('username' => 'facebook_id','password' => 'facebook_access_token');
	 	parent::beforeFilter();//親クラスを継承
	 	$this->set('user',$this->Auth->user()); // ctpで$userを使えるようにする 。
	 	$this->set('action',$this->action);
	}

	/***ログイン前ページ****/
	//PC版のトップページ
	function start(){
		$this->authredirect(); //f1
		$this->layout = 'taxshare';
		echo "aaa";
	}
	
	//m1.モバイル版のトップページ
	function mobile(){
		$this->layout = null;
		$this->Session->write('LoginType',"mobile");
		$this->authredirect();//f1
		//$this->redirect('/taxshare_m/main');
	}
	
	/***ログイン後ページ***/
	//m2.mainpage
	function main(){
		//モバイル振り分け
		if($this->Session->read('LoginType') == "PC"){
			$this->redirect('/taxshare/main/');
		}elseif($this->Session->read('LoginType') == "mobile"){
		//	$this->redirect('/taxshare_m/main/');
		}
		
		
		//if($this->Auth->user('driver') == 0){
		//	$this->redirect('option');
	 	//}
		$data = $this->TsSchedule->find('all',array('limit' => 6,'order' => 'id desc'));
		$this->set('data', $data);
		
	}
	
	//m3-1.driver
	function driver(){
		
	}
	
	//m4-1.passenger
	function passenger(){
		
	}
	
	//m3&4-2 schedule登録
	function schedule(){
		//driverページ passengerページ以外からくるとリダイレクト
	/*	$venue = $this->TsVenue->find('all',array('limit' => 5));
		$this->set('venue',$venue);
		
		if(empty($this->params['form']['flag'])){
			$this->redirect('main');
		}else{
			$this->set('flag',$this->params['form']['flag']);
		}	*/
	}
	
	function checkschedule(){
		//投稿処理
		if(!empty($this->data)){
			$this->TsSchedule->save($this->data); //DB登録
			//facebookに共有on
			if($this->params['form']['fbshare'] == 'on'){
				$this->facebook = $this->createFacebook();
				$user = $this->Auth->user();
				if($this->data['TsSchedule']['fixeddate'] == 1){
					$mes = "移動したいです．"; //1 = のりたい pssenger
				}else{
					$mes = "移動します．";	 //2 = のせます driver
				}
				$attachment =  array(
    				'access_token' => $user['TsUser']['facebook_access_token'],
    				'message' => "hoge",
    				'name' => "タクシェア",
    				'link' => "http://n0.x0.to/rsk/taxshare/mobile/",
    				'description' => "",
    				'picture'=> ""
				);
				$this->facebook->api('/me/feed', 'POST', $attachment);
			}
		}
		$this->redirect('main');
	}
	
	//m3&4-3 search
	function search(){
		
	}
	
	function option(){
		$this->layout = null; //共通レイアウトは使用しない
	}
	
	function top(){//トップページ ログイン前
		$this->layout = null; //共通レイアウトは使用しない
		//ログインしている状態ならindexへ
		$loginflag = $this->Session->read('loginflag');
		if(!empty($loginflag)){$this->redirect('mobile');}
	}
	
	function index(){//トップページ 貸し出し一覧
	/*
		$item = $this->SrRental->find('all', array('conditions' => 
			array('SrRental.status <=' => '1', //ステータス1以下(ステータス0公開中 1レンタル申請中)
				'OR' => array( //レンタル申請がされてないもの一覧．ただし、自分が申請してるものは表示させる
							array('SrRental.request_user' => null),
							array('SrRental.request_user' => $this->Auth->user('id'))
						),
				'NOT' => array('SrRental.user_id' => $this->Auth->user('id'))//自分の物品以外
			))); //レンタル可能物品
		$this->set('item',$item);*/
		
	}
	//詳細ページ
	function detail(){
		$this->mainredirect(); //idが無ければリダイレクト
		//予定
		$data = $this->TsSchedule->find('first',array('conditions' => array('id'  => $this->params['url']['id'])));//情報取得
		
		if($data['TsSchedule']['state'] < 2){//締切前
			$this->set("data",$data);
		}else{ //締切後なら
			$this->set("data",null);
		}
		//ユーザ情報
		$commiter = $this->TsUser->find('first',array('conditions' => array('id'  => $data['TsSchedule']['user_id'])));//情報取得
		$this->set("commiter",$commiter); 
		//コンタクト情報
		$contact = $this->TsContact->query(
		'SELECT DISTINCT orderer_id,comment,username,facebook_id
		 FROM ts_contacts LEFT JOIN ts_users
		 ON ts_contacts.orderer_id = ts_users.id
		 WHERE ts_contacts.schedule_id = '. $this->params['url']['id'] .'
		 ORDER BY  `ts_contacts`.`created` DESC 
		;');
		$this->set("contact",$contact);
	}

	function check(){
		$this->layout = null;
	}
	//投稿削除機能
	function delete(){
		$this->mainredirect(); //idが無ければリダイレクト
		$this->TsSchedule->delete($this->params['url']['id']);
		$this->redirect('main');
	}
	//投稿編集
	function edit(){
		//$this->mainredirect(); //idが無ければリダイレクト
		//ベニュー取得
		$this->set('venue',$this->TsVenue->find('all',array('order' => 'id desc')));
		//入力情報
		$data = $this->TsSchedule->find('first',array('conditions' => array('id'  => $this->params['url']['id'])));//情報取得
		$this->set("data",$data); 
		//エラーメッセージ
		$this->set('error',$this->params['url']['error']);
		
		//投稿処理
		if(!empty($this->data)){
			//バリデーション
			$this->validation($this->data);
			//予定日と時刻を統合
			$this->data['TsSchedule']['fixeddate'] = $this->data['TsSchedule']['fixeddate']['date'].' '.$this->data['TsSchedule']['fixeddate']['time'];
			$this->data['TsSchedule']['limit'] = $this->data['TsSchedule']['limit']['date'].' '.$this->data['TsSchedule']['limit']['time'];
			if($this->data['TsSchedule']['title'] == 'passenger'){
				$this->data['TsSchedule']['dpflag'] = "0"; //passenger=0
				$this->data['TsSchedule']['title'] = "乗せて!";
			}else{
				$this->data['TsSchedule']['dpflag'] = "1"; //driver=1
				$this->data['TsSchedule']['title'] = "乗せるよ!";
			}
			
			$this->TsSchedule->save($this->data); //DB登録
			//facebookに共有on
			if($this->params['form']['fbshare'] == 'on'){
				$this->facebook = $this->createFacebook();
				$user = $this->Auth->user();
				if($this->data['TsSchedule']['fixeddate'] == 1){
					$mes = "移動したいです．"; //1 = のりたい pssenger
				}else{
					$mes = "移動します．";	 //2 = のせます driver
				}
				$attachment =  array(
    				'access_token' => $this->Auth->user('facebook_access_token'),
    				'message' => "hoge",
    				'name' => "タクシェア",
    				'link' => $this->indexUrl,
    				'description' => "",
    				'picture'=> ""
				);
				$this->facebook->api('/me/feed', 'POST', $attachment);
			}
			
			//twitterに共有on
			if($this->params['form']['twshare'] == 'on'){
				$tweet = $this->indexUrl; //twitter投稿内容
				$this->twpost($tweet); //twitterへ投稿
			}
			$this->redirect('main');
		}
		
	}
	public function mainredirect(){
		if(empty($this->params['url']['id'])){
			$this->redirect('main');
		}
	}

	/***facebookログイン周り****/
		
	function logout(){//ログアウト処理
		$this->Auth->logout();
		$this->Session->destroy();
		$this->redirect('mobile');
	}
	
	
	 public function facebook(){//facebookの認証処理部分
		$this->autoRender = false;
		$this->facebook = $this->createFacebook();
		$url = $this->facebook->getLoginUrl(array('redirect_url' => 'http://localhost/study/users/callback/','scope' => 
				'email,publish_stream','canvas' => 1,'fbconnect' => 0));
       	$this->redirect($url);
	}
	 
	public function callback(){//facebookの認証処理部分
 		$this->facebook = $this->createFacebook();//インスタンス生成
    	$me = null;
		$user = $this->facebook->getUser();//ユーザ情報取得
    	if($user){
    		$me = $this->facebook->api('/me');//ユーザ情報取得
    	}else{}
    	$access_token = $this->facebook->getAccessToken();//access_token入手
    	//$test = $this->User->find('first',array('conditions' => array('facebook_id' => $me['id'])));
    	
		if($this->Auth->user()){//既にユーザー登録済みの場合，
			$me['name'] = $this->Auth->user('username');//登録済みのユーザー情報を利用
		}

    	$result = $this->TsUser->facebook(
				Array(
				"id" => $this->Auth->user('id'), //更新処理
        		'facebook_id' => $me['id'],
            	'username' => $me['name'],
            	'location' => $me['location']['id'],
            	'facebook_access_token' =>  $access_token,
            	'email' => $me['email'],
            	'sex' => $me['gender']
            	)
            );//facebookのデータからユーザ登録を行う

		$this->data['TsUser']['facebook_id'] = $me['id'];
		$this->data['TsUser']['facebook_access_token'] = $access_token;
		
		if ($this->Auth->login($this->data)){//Authの処理
			//$this->redirect($this->Auth->redirect());//ログイン成功したら、リダイレクト
			$this->redirect('/taxshare_m/main');
		} else {
			$this->redirect('mobile');//失敗したら、元のページへもどる
		}
	}

	private function createFacebook() {//ここにfacebookのアプリ登録をしたときに得られるアプリのID, アプリの秘訣を書く。
         return new Facebook(array(
        //'appId'  => '121958047958657',
        //'secret' => '47afa36cd09332a352e9428cc8b0c210'
        	'appId'  => '145210128957576',
        	'secret' => 'f373188cf79b2d46839b2838375109ea'
    	));
    }

	/****facebook post****/
	public function post() {//facebookのwallにpostする処理
		if (!empty($this->data)) {
			$data =$this->Auth->user();
			$user = $this->Example->findById($data['Example']['id']);
            $this->facebook = $this->createFacebook();
			$attachment =  array(
    			'access_token' => $user['Example']['facebook_access_token'],
    			'message' => $this->data['Example']['facebook_wall'],
    			'name' => "name test",
    			'link' => "",
    			'description' => "here description",
    			'picture'=> "http://example.jp/image.jpg"
			);
			$this->facebook->api('/me/feed', 'POST', $attachment);
			//$this->flash('マイページに戻ります','/examples/test/');
		}
	}
	
}