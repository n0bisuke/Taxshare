<?php 
/*読み込み*/
App::import('Vendor', 'oauth', array('file' => 'OAuth'.DS.'oauth_consumer.php'));//twitter認証
//App::import('Vendor', 'upgrade');//json_decode()を読み込み、twitter_callbackで利用
App::import('Vendor', 'facebook',array('file' => 'facebook'.DS.'src'.DS.'facebook.php'));//facebook認証

class TaxshareController extends AppController {
	public $name = 'Taxshare';
	public $uses = array('TsUser','TsSchedule','TsVenue','TsContact','TsBoard');
	public $components = array('Auth');
	public $layout = 'taxshare';
	public $indexUrl = 'https://www.facebook.com/taxshare';
	
	public function authredirect(){ //[F1] ログインしている場合に強制的にmainに移動させる． 
			$user = $this->Auth->user();
			if(!empty($user)){
				$this->redirect('main');
			}
	}
	
	function beforeFilter(){//login処理の設定
	 	$this->Auth->allow('index','top','facebook','callback');//ログインしないで、アクセスできるアクションを登録する
	 	$this->Auth->loginRedirect = array('controller' => 'taxshare','action' => 'main');
    	$this->Auth->logoutRedirect = array('controller' => 'taxshare','action' => 'index');
    	$this->Auth->loginAction = array('controller' => 'taxshare','action' => 'index');
	 	//facebookでのログインテーブルとフィールド設定
	 	$this->Auth->userModel = 'TsUser';
		$this->Auth->fields = array('username' => 'facebook_id','password' => 'facebook_access_token');
	 	parent::beforeFilter();//親クラスを継承
	 	$this->set('user',$this->Auth->user()); // ctpで$userを使えるようにする 。
	 	$this->set('action',$this->action);
		date_default_timezone_set('Asia/Tokyo');//date関数で時間がずれる現象に対処
	}

	/***ログイン前ページ****/
	//トップページ ログインもここから
	function index(){
		$this->layout = null;
		$this->Session->write('LoginType',"PC");
		$this->authredirect();//f1
	}
	
	/***ログイン後ページ***/
	//m2.mainpage
	function main(){
		//モバイル振り分け
		if($this->Session->read('LoginType') == "PC"){
			//$this->redirect('/taxshare/main/');
		}elseif($this->Session->read('LoginType') == "mobile"){
			$this->redirect('/taxshare_m/main');
		}
		
		//デフォルト検索条件
		$WHERE = "WHERE "
				."ts_schedules.note != ' '" //noteが空のモノは表示しない
				."AND ts_schedules.limit > now()" //受付期限が過去のものは表示しない
				."AND ts_schedules.state <= 1" //完了済みのものは表示しない
				;
		//検索条件
		if(!empty($this->params['form']['passenger'])){
			$WHERE .= "AND ts_schedules.dpflag = 0"; //passenger検索
		}elseif(!empty($this->params['form']['driver'])){
			$WHERE .= "AND ts_schedules.dpflag = 1"; //driver検索
		}elseif(!empty($this->params['form']['level1'])){
			$WHERE .= "AND ts_schedules.important_lv = 1";//普通
		}elseif(!empty($this->params['form']['level2'])){
			$WHERE .= "AND ts_schedules.important_lv = 2";//急いでる
		}elseif(!empty($this->params['form']['level3'])){
			$WHERE .= "AND ts_schedules.important_lv = 3";//緊急
		}
		else{
		}

		$data = $this->TsSchedule->query(
			'SELECT * FROM ts_schedules 
			'. $WHERE .'
		 	ORDER BY  `ts_schedules`.`id` DESC 
		;');
		$this->set('data', $data);
		
		//$this->TsContact->find('count');
		//debug($data);
	}
	
	
	
	//m3&4-2 schedule登録
	function schedule(){
		//ベニュー取得
		$this->set('venue',$this->TsVenue->find('all',array('order' => 'id desc')));
		$this->set('error',$this->params['url']['error']);
		//投稿処理
		if(!empty($this->data)){
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
	//締切後ページ
	function twostate(){
		if(!empty($this->params['form']['id'])){
			$data = $this->TsSchedule->query(
			'SELECT *
		 	FROM ts_contacts inner JOIN ts_schedules
		 	ON ts_contacts.schedule_id = ts_schedules.id
		 	inner JOIN ts_users
		 	ON ts_contacts.orderer_id = ts_users.id
		 	WHERE ts_contacts.schedule_id = '. $this->params['form']['id'] .'
			;');
			//<!--スレッド参加者以外を追い出し処理--> And ts_contacts.state = 1
			//ログインユーザが投稿者ならば
			if($this->Auth->user('id') == $data[0]['ts_schedules']['user_id']){
				$flag = 1;
			}else{
				$flag = 0; //ログインユーザが関係（コンタクト済みでも投稿者でも)無い
				foreach ($data as $key => $value){
					if($this->Auth->user('id') == $value['ts_contacts']['orderer_id']){
						$flag = 2; //ログインユーザがコンタクト済みならば
						break;
					}
				}
			}
			//stateが2だったら，(マッチング済だったら
			if($data[0]['ts_contacts']['state'] >= 2){
				$this->redirect('threestate?id='.$this->params['form']['id']
				."&user_id=".$this->params['form']['user_id']
				."&user_fb_id=".$this->params['form']['user_fb_id']
				); //3stateへリダイレクト
			}
			if($flag == 0) $this->redirect('main'); //追い出し
			//<!--ここまででスレッド参加者以外を追い出し処理-->
			
			$data[0]['TsSchedule'] = $data[0]['ts_schedules'];
			$this->set('data',$data[0]);
			$this->set('contact',$data);
			$this->set('hostuser',array('user_id' => $this->params['form']['user_id'],'user_fb_id' => $this->params['form']['user_fb_id']));
			
			//<!--掲示板処理-->
			if(!empty($this->params['form']['comment'])){
				$this->data['TsBoard']['user_id'] = $this->Auth->user('id');
				$this->data['TsBoard']['schedule_id'] = $this->params['form']['id'];
				$this->data['TsBoard']['comment'] = $this->params['form']['comment'];
				$this->TsBoard->save($this->data);
			}
			$board = $this->TsBoard->find('all',array('conditions' => array("schedule_id" => $this->params['form']['id'])));			
			//debug($board);
			$this->set('board',$board);

		}else $this->redirect('main'); //URL直アクセス規制
	}
	
	//締切後 state3 評価してくれ。
	function threestate(){
		$this->params['url'] = (!empty($this->params['form']['id']))? $this->params['form']: $this->params['url'];
		
		if(!empty($this->params['url']['id'])){
			$data = $this->TsSchedule->query(
			'SELECT *
		 	FROM ts_contacts inner JOIN ts_schedules
		 	ON ts_contacts.schedule_id = ts_schedules.id
		 	inner JOIN ts_users
		 	ON ts_contacts.orderer_id = ts_users.id
		 	WHERE ts_contacts.schedule_id = '. $this->params['url']['id'] .'
			;');

			//<!--スレッド参加者以外を追い出し処理-->
			//ログインユーザが投稿者ならば
			if($this->Auth->user('id') == $data[0]['ts_schedules']['user_id']){
				$flag = 1;
			}else{
				$flag = 0; //ログインユーザが関係（コンタクト済みでも投稿者でも)無い
				foreach ($data as $key => $value){
					if($this->Auth->user('id') == $value['ts_contacts']['orderer_id']){
						$flag = 2; //ログインユーザがコンタクト済みならば
						break;
					}
				}
			}
			//if($flag == 0) $this->redirect('main'); //追い出し
			//<!--ここまででスレッド参加者以外を追い出し処理-->
			
			$data[0]['TsSchedule'] = $data[0]['ts_schedules'];
			$this->set('data',$data[0]);
			$this->set('contact',$data);
			$this->set('hostuser',array('user_id' => $this->params['url']['user_id'],'user_fb_id' => $this->params['url']['user_fb_id']));
		}else $this->redirect('main'); //URL直アクセス規制
	}
	
	//締切 stateを2にします．
	function limit(){
		if($this->params['form']['id']){
			//stateを更新
			$this->data['TsSchedule']['id'] = $this->params['form']['id']; //更新用
			$this->data['TsSchedule']['state'] = 2; //2=締切
			$this->TsSchedule->save($this->data);
		}
		$this->redirect('main');
	}
	//コンタクトをとる。 stateを1にする 既にコンタクトを取っている場合は追加しない．
	function contact(){
		//orderer発注者
		$orderer = $this->Auth->user('username'); //コンタクトを取った側のユーザーname
		$orderer .= "(@". $this->Auth->user('twitter_id') .")"; //コンタクトを取る側がtwitterアカウントがあれば。
		//accepter受注者
		$accepter_data = $this->TsVenue->query('SELECT * FROM `ts_users` WHERE `id` IN (SELECT `user_id` FROM `ts_schedules` WHERE `id` = '. $this->params['form']['id'] .')');
		$accepter = $accepter_data[0]['ts_users']['username']; //コンタクトを受けた側のユーザーname
		$accepter .= "(@". $accepter_data[0]['ts_users']['twitter_id'].")"; //コンタクトを受けた側がtwitterアカウントがあれば。
		
		//既に登録してある場合のskip処理
		$check = $this->TsContact->find('first',array('conditions' => array(
			"schedule_id" => $this->params['form']['id'],
			"accepter_id" => $accepter_data[0]['ts_users']['id'],
			"orderer_id" => $this->Auth->user('id')
			)));
		
		if(empty($check)){ //未登録の場合
			//contactを作成
			$this->data['TsContact']['orderer_id'] = $this->Auth->user('id');
			$this->data['TsContact']['accepter_id'] = $accepter_data[0]['ts_users']['id'];
			$this->data['TsContact']['schedule_id'] = $this->params['form']['id'];
			$this->data['TsContact']['comment'] = $this->params['form']['comment'];
			$this->data['TsContact']['d_point'] = 0;//ポイント初期化
			$this->data['TsContact']['p_point'] = 0;//ポイント初期化
			$this->data['TsContact']['state'] = 1; //1=気になる ってことにしようかな．．
			$this->TsContact->save($this->data);
		
			//stateを更新
			$this->data['TsSchedule']['id'] = $this->params['form']['id']; //更新用
			$this->data['TsSchedule']['state'] = 1; //2=締切
			$this->TsSchedule->save($this->data);
			
			$tweet = $accepter ."の投稿を". $orderer ."がチェックしました． http://faceboo.com/taxshare/ #taxshare";
			$this->twbotpost($tweet);
		}

		$this->redirect('main');
	}
	
	function beforedecide(){
		$this->set("params",$this->params['url']);
	}
	
	//コンタクトを決定する．stateを2にする 自分の予定について
	function decide(){
		//更新用 
		$check = $this->TsContact->find('first',array('conditions' => array(
			"schedule_id" => $this->params['url']['id'],
			"accepter_id" => $this->Auth->user('id'),
			"orderer_id" => $this->params['url']['user_id']
			)));
		//contactを更新
		$this->data['TsContact']['orderer_id'] = $this->params['url']['user_id']; //コンタクトを取った側のユーザーname
		$this->data['TsContact']['accepter_id'] = $this->Auth->user('id'); //(予定を登録したログインユーザ)
		$this->data['TsContact']['schedule_id'] = $this->params['url']['id'];
		$this->data['TsContact']['comment'] = "";
		$this->data['TsContact']['id'] = $check['TsContact']['id'];
		$this->data['TsContact']['state'] = 2; //2=確定ってことにしようかな．．
		$this->TsContact->save($this->data);		
		//stateを更新
		$this->data['TsSchedule']['id'] = $this->params['url']['id']; //更新用
		$this->data['TsSchedule']['state'] = 2; //2=締切
		$this->TsSchedule->save($this->data);
		//$tweet = $accepter ."が". $orderer ."と． http://faceboo.com/taxshare/ #taxshare";
		//$this->twbotpost($tweet);
		
		$this->redirect('threestate?id='.$this->params['url']['id'].'&user_fb_id='. $this->params['url']['user_fb_id']);// ラストstateへ
	}
	//ユーザの評価
	function evaluate(){
		if($this->data){
			//ポイント情報を更新
			$this->data['TsContact']['comment'] = $this->data['TsContact']['comment'];
			if(!empty($this->data['TsContact']['d_point'])){
				$this->data['TsContact']['d_point'] = $this->data['TsContact']['d_point'];	
			}else{
				$this->data['TsContact']['p_point'] = $this->data['TsContact']['p_point'];				
			}
			$this->data['TsContact']['id'] = $this->data['TsContact']['id'];
			$this->TsContact->save($this->data);
			
			//ポイント情報
			$pointinfo = $this->TsContact->find('first',array('conditions'=>array("id" => $this->data['TsContact']['id'])));
			debug($pointinfo);			
			//お互いに評価が終了した場合にTsScheduleのstateを更新
			if(($pointinfo['TsContact']['d_point'] > 0) && ($pointinfo['TsContact']['p_point'] > 0)){
				$this->data['TsSchedule']['id'] = $this->data['TsContact']['schedule_id']; //更新用
				$this->data['TsSchedule']['state'] = 3; //3評価おわり．
				$this->TsSchedule->save($this->data);
				$this->data['TsContact']['state'] = 3; //3=終了ってことにしようかな．．
			}

			$this->data['TsContact']['id'] = $this->data['TsContact']['id'];
			$this->TsContact->save($this->data);
			
			$this->redirect("main");
		}
	}
	
	function myschedule(){
		$my = $this->TsContact->query(
		'SELECT orderer_id,comment,username,facebook_id,schedule_id,twitter_id
		 FROM ts_contacts LEFT JOIN ts_users
		 ON ts_contacts.orderer_id = ts_users.id
		 WHERE ts_contacts.accepter_id = '. $this->Auth->user('id') .'
		 ORDER BY  `ts_contacts`.`created` DESC 
		;'
		);
		$this->set('my',$my);
	}
	
	//ユーザ詳細ページ
	function userprof(){
		if($this->params['url']['id']){
			$id = $this->params['url']['id'];
		}else{
			$id = $this->Auth->user('id');
		}
		$data = $this->TsContact->query(
		'SELECT *
		 FROM ts_users LEFT JOIN ts_schedules
		 ON ts_users.id = ts_schedules.user_id
		 WHERE ts_users.id = '. $id .'
		;');
		$this->set("data",$data);
		/*
		$pointinfo = $this->TsContact->query(
			'SELECT d_point,p_point
		 	FROM ts_contacts LEFT JOIN ts_users
		 	ON ts_contacts.orderer_id = ts_users.id
		 	WHERE ts_contacts.accepter_id = '. $this->Auth->user('id') .'
		 	ORDER BY  `ts_contacts`.`created` DESC 
			;'
		);*/
		
		
	}
	
	function myhistory(){}
	function setting(){}
	//投稿削除機能
	function delete(){
		$this->mainredirect(); //idが無ければリダイレクト
		$this->TsSchedule->delete($this->params['url']['id']);
		$this->redirect('main');
	}
	
	//通知
	function notice(){
		
	}
	
	//コンタクトリスト
	function favorite(){
		//検索条件
		if(!empty($this->params['form']['state2'])){
			$state = 2; //交渉中
			$mes = "あなたがコンタクトを取って交渉中の予定";
		}elseif(!empty($this->params['form']['state3'])){
			$state = 3; //交渉中
			$mes = "あなたがコンタクトを取って完了した予定";
		}else{
			$state = 1; //コンタクトをしたもの
			$mes = "あなたがコンタクトを取っている予定";
		}
		
		//コンタクトを取った予定を検索．
		$data = $this->TsContact->query(
		'SELECT *
		 FROM ts_contacts LEFT JOIN ts_schedules
		 ON ts_contacts.schedule_id = ts_schedules.id
		 WHERE ts_contacts.orderer_id = '. $this->Auth->user('id') .'
		 AND ts_schedules.state = '. $state .'
		 ORDER BY  `ts_contacts`.`created` DESC 
		;' //state = 1 コンタクトを取った予定
		);
		//debug($data);
		$this->set('data',$data);
		$this->set('mes',$mes);
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
	
	function top(){//トップページ ログイン前
		$this->layout = null; //共通レイアウトは使用しない
		//ログインしている状態ならindexへ
		$loginflag = $this->Session->read('loginflag');
		if(!empty($loginflag)){$this->redirect('index');}
	}
	
	/**
	 * 
	 * 
	 * 各ページ以外の機能
	 * 
	 * 
	 * 
	 */
	
	
	/***
	 * リダイレクト関数
	 */ 
	public function mainredirect(){
		if(empty($this->params['url']['id'])){
			$this->redirect('main');
		}
	}
	
	
	
	
	
	/***
	 * バリデーション
	 */
	public function validation($inputdata){
		$setmsg="";
		$this->Session->write('error',''); //エラーメッセージ初期化
		//title
		if($inputdata['TsSchedule']['title'] != ('passenger' || 'driver')){
			$setmsg .= "・タイトルの設定が不正です<br />";
		}
		//fixeddate 予定時刻が現在時刻よりも過去の場合 はエラー
		$inputtime = strtotime($inputdata['TsSchedule']['fixeddate']['date'].' '.$inputdata['TsSchedule']['fixeddate']['time']);
		$nowtime = strtotime(date('Y-m-d H:i'));
		if($inputtime < $nowtime){
			$setmsg .= "・予定時刻は現在時刻より過去には設定できません<br />";
		}
		//limit 受付期限が予定時刻よりも先の場合 また 受付期限が現在時刻よりも過去の場合 はエラー
		$limittime = strtotime($inputdata['TsSchedule']['limit']['date'].' '.$inputdata['TsSchedule']['limit']['time']);
		$nowtime = strtotime(date('Y-m-d H:i'));
		if($limittime < $nowtime){
			$setmsg .= "・受付期限は現在より過去には設定できません<br />";
		}elseif($limittime > $inputtime){
			$setmsg .= "・受付期限は予定時刻よりも未来には設定できません<br />";
		}
		//point 出発地点or目的地が設定されていない場合　はエラー
		if(empty($inputdata['TsSchedule']['startpoint']) || empty($inputdata['TsSchedule']['endpoint'])){
			$setmsg .= "・場所の設定が不正です<br />";
		}
		//note 備考が設定されていない場合　はエラー
		if(empty($inputdata['TsSchedule']['note'])){
			$setmsg .= "・備考を入力して下さい<br />";
		}
		
		//評価 $setmsgにメッセージが入っている場合はエラー
		if($setmsg != ""){
			$this->redirect($this->action.'?error='.$setmsg.'&id='.$inputdata['TsSchedule']['id']); //リダイレクト
		}
	}
	
	
	/***
	 * facebookログイン周り**
	 * **/
		
	function logout(){//ログアウト処理
		$this->Auth->logout();
		$this->Session->destroy();
		$this->redirect('index');
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
			$this->redirect('/taxshare/main');
		} else {
			$this->redirect('index');//失敗したら、元のページへもどる
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
	/*
	 * wikipedia api を使ってみる
	 * */
	function wikiapi(){
		function wikipediaApi($keyword){
			$keyword = h($keyword);
			$url = sprintf('http://wikipedia.simpleapi.net/api?output=php&keyword=%s', urlencode($keyword));
			debug($url);
			$data = file_get_contents($url);
			debug($data);
			if($data === false) {
				return false;
			} else {
				return unserialize($data);
			}
		}
		debug((wikipediaApi('岩手県立大学')));
	}
	
	
	
	/******
	 * 4sq api を使ってみる
	 */
	function foursqapi(){
		//$client_id = "4RTUFU50ZDV00GZAZE4PKZSKXKPQBTUPDYLOHC211YH25EUL";
		//$client_secret = "MI1443SAR2NENJLUXLLXTDD0K0203RRRFREOPCYJRWUHHMWB";
		//$callback_url = "http://*******************/apitest.php";
		$access_token = "DOUYYFHDSMXK1LTICS0P5JAJZN5WK3TQPGFEAJP3FSH5L2MJ";
		
		$ll = '39.801233,141.137605';
		//リクエスト先
		$search_venues_url = "https://api.foursquare.com/v2/venues/search?ll=" . $ll 
		. "&limit=". '50'
		. "&oauth_token=" . $access_token;
		//JSON形式で周辺venue情報を取得し配列に
		$venues_array = json_decode(file_get_contents($search_venues_url), true);
		debug($venues_array['response']['groups'][0]['items']);
		foreach ($venues_array['response']['groups'][0]['items'] as $key => $value) {
			//debug($value['name']);
		}
		//debug($venues_array['response']['groups'][0]['items']);

	}
	public function foursq_callback(){
		
	}
	
		
    /**************************************
    twitterの認証処理
    ***************************************/
    public function twitter() {//twitterの認証処理部分
     	$consumer = $this->createConsumer();//インスタンス生成する
        $requestToken = $consumer->getRequestToken('https://api.twitter.com/oauth/request_token', 'http://www31092u.sakura.ne.jp/~offside_now/rsk/taxshare/twitter_callback/');
        //requestトークン取得
        $this->Session->write('twitter_request_token', $requestToken);//requestトークンをセッションに保存
        $this->redirect('https://api.twitter.com/oauth/authorize?oauth_token=' . $requestToken->key); //twitterにリダイレクト
    }
	
	/**************************************
    twitterの認証処理
	 * userid: taxshare
	 * passwd: taxsharepbl
	 * email: o3275218@rtrtr.com ← 10mail
    ***************************************/	      
    public function twitter_callback() {//twitterの認証処理部分
		$requestToken = $this->Session->read('twitter_request_token');//requestトークンを取得
     	$consumer = $this->createConsumer();//インスタンス生成
		$accessToken = $consumer->getAccessToken('https://api.twitter.com/oauth/access_token', $requestToken);//アクセストークン取得
		$json = $consumer->get($accessToken->key, $accessToken->secret, 'http://api.twitter.com/1/account/verify_credentials.json', array());
		$twitterData = json_decode($json, true);
		
		//twitter情報を登録
		$this->data['TsUser']['id'] = $this->Auth->user('id'); //更新処理
		$this->data['TsUser']['twitter_id'] = $twitterData['screen_name'];
		$this->data['TsUser']['twitter_access_key'] = $accessToken->key;
		$this->data['TsUser']['twitter_access_secret'] = $accessToken->secret;
		$this->TsUser->save($this->data);
		//利用者のtwitterアカウントと@taxshareが相互フォローする
		$this->twfollow($twitterData['screen_name'],$accessToken->key,$accessToken->secret);
		//一度ログアウトして再ログイン
		$this->Auth->logout();
		$this->Session->destroy();
		$this->redirect('facebook');
    }
	
	private function createConsumer() {//ここにtwitterのアプリ登録をしたときに得られるコンシューマーキー, シークレットキーを書く。
		return new OAuth_Consumer('4WB9b8EcnzLJCmu4MzzLg', 'o3YHWnwUXs1wG8DAUcQg1padrq0kt2NBjWsreDPDJE');
	}
	
	function twdeconnect(){//twitterの情報削除
		//情報を初期化
		$this->data['TsUser']['id'] = $this->Auth->user('id'); //更新処理
		$this->data['TsUser']['twitter_id'] = null;
		$this->data['TsUser']['twitter_access_key'] = null;
		$this->data['TsUser']['twitter_access_secret'] = null;
		$this->TsUser->save($this->data);
		//一度ログアウトして再ログイン
		$this->Auth->logout();
		$this->Session->destroy();
		$this->redirect('facebook');
	}
	
	public function twDM($dm,$user){//DM関数 @taxshare -> 利用者へ DM
		$consumer = $this->createConsumer();
		$consumer->post('929605698-PCXTQNkPFDJ3IDalmiBUkRTKuthCGIiBCvAtFvvc','TJhZjO8pjDpp82RntCAoC3I0MzQoOM1u5PQcBbBk5s', 
		'http://api.twitter.com/1/direct_messages/new.json', array('screen_name' => $user,'text' => $dm));
	}
	
	public function twbotpost($tweet){//@taxshareがツイートする関数 @taxshare -> (tweet) 
		$consumer = $this->createConsumer();
		$consumer->post('929605698-PCXTQNkPFDJ3IDalmiBUkRTKuthCGIiBCvAtFvvc','TJhZjO8pjDpp82RntCAoC3I0MzQoOM1u5PQcBbBk5s', 
		'http://api.twitter.com/1/statuses/update.json', array('status' => $tweet));
	}
	
	public function twfollow($twuser,$twitter_access_key,$twitter_access_secret){//相互フォロー関数
		$consumer = $this->createConsumer();
		//フォロー 利用者 -> @taxshare
		$consumer->post($twitter_access_key, $twitter_access_secret, 'http://api.twitter.com/1/friendships/create.json', array('screen_name' => 'taxshare'));
		//フォロー返し @taxshare -> 利用者
		$consumer->post('929605698-PCXTQNkPFDJ3IDalmiBUkRTKuthCGIiBCvAtFvvc','TJhZjO8pjDpp82RntCAoC3I0MzQoOM1u5PQcBbBk5s', 
		'http://api.twitter.com/1/friendships/create.json', array('screen_name' => $twuser));
	}
	
	public function twpost($tweet){//利用者のtwitterへのつぶやき関数
		$consumer = $this->createConsumer();
		$consumer->post($this->Auth->user('twitter_access_key'), $this->Auth->user('twitter_access_secret'),
		'http://api.twitter.com/1/statuses/update.json', array('status' => $tweet));
	}
}