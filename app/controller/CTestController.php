<?php
class CTestController extends CBaseController
{

	public function indexAction()
	{	
		echo microtime()."<br>";
		$user = CMUser::model()->get(1);
		var_dump($user);
		$user->save();
		//CMUser::add()->setKey(1000001)->setName('dadsadsdsas')->save();
		var_dump(CMUser::model()->get(1000001));
		echo microtime()."<br>";;
	}
	
	public function productAction()
	{
		$product = CProduct::model()->get(1);
		$product->save(true);
		var_dump($product);
	}
	
	public function redisAction()
	{
		//CSpp::getInstance()->getLogger()->debug('request start');
		echo microtime();
		var_dump(CRUser::model()->setKey('1111')->setHead('yyyyyyyyyyyyyy')->setName('starjiang1')->save());
		var_dump(CRUser::model()->setKey('1112')->setHead('yyyyyyyyyyyyyy')->setName('starjiang1')->save());
		var_dump(CRUser::model()->setKey('1113')->setHead('yyyyyyyyyyyyyy')->setName('starjiang1')->save());
		
		echo microtime();
		var_dump(CRUser::model()->get('11111'));
		var_dump(CRUser::mget(array('1111','1112','1113','11146')));
		CRUser::add()->setKey('1111')->save();
		echo microtime();
	}
	
	public function lbredisAction()
	{
		//CSpp::getInstance()->getLogger()->debug('request start');
		echo microtime();
		//var_dump(CLBRUser::model()->setKey('1111')->setHead('yyyyyyyyyyyyyy')->setName('starjiang1')->save());
		//var_dump(CLBRUser::model()->setKey('1112')->setHead('yyyyyyyyyyyyyy')->setName('starjiang1')->save());
		//var_dump(CLBRUser::model()->setKey('1113')->setHead('yyyyyyyyyyyyyy')->setName('starjiang1')->save());
		//var_dump(CLBRUser::model()->setKey('1114')->setHead('yyyyyyyyyyyyyy')->setName('starjiang1')->save());
		
		echo microtime();
		var_dump(CLBRUser::model()->get('1111'));
		var_dump(CLBRUser::mget(array('1111','1112','1113','1117')));
		var_dump(CLBRUser::model()->delete('1111'));
		echo microtime();
	}
	
	public function aAction()
	{
		echo microtime()."<br/>";
		CCReader::init(CConfig::$shmMKey,CConfig::$shmSKey);
		echo microtime()."<br/>";
		CCReader::get("cfg.items.item1");
		var_dump(CCReader::mget(array("cfg.items.item1","cfg.items.item2","cfg.items.item41")));
		echo microtime()."<br/>";
		echo microtime()."<br/>";

		var_dump(CCReader::get("cfg.items"));
		var_dump(CCReader::get("cfg.sys.host"));
		var_dump(CCReader::get("cfg.sys.port"));
		var_dump(CCReader::get("cfg.sys.info"));
		var_dump(CCReader::get("cfg.items.item1"));
		var_dump(CCReader::get("cfg.events.Event1"));

	}
	
	public function mongoAction()
	{
		$this->data['title'] = 'HelloWorld';
		$this->data['name'] = 'starjiang';
		$this->data['birth'] = '1984/02/10';
	
		$user=CPlayer::model();
		$user->_id='starjiang1';
		$user->nickName='xxxxx1';
		$user->headPic='http://xxx1';
		$user->save();
	
		$user=CPlayer::model();
		$user->_id='starjiang2';
		$user->nickName='xxxxx2';
		$user->headPic='http://xxx2';
		$user->set_id('starjiang2')->setNickName('ssss');
	
		$user->save();
	
		$user=CPlayer::model();
		$user->_id='starjiang3';
		$user->nickName='xxxxx3';
		$user->headPic='http://xxx3';
	
		$user->save();
	
		//$user=CPlayer::model();
		//$user->userId='starjiang5';
		//$user->nickName='xxxxx5';
		//$user->headPic='http://xxx5';
	
		//$user->save();
	
	
		$user2=CPlayer::model()->get('starjiang1');
	
		var_dump($user2);
	
		$users=CPlayer::mget(array('starjiang1','starjiang2','starjiang3','starjiang5'));
	
		var_dump($users);
		
		$users=CPlayer::query(array('nickName'=>'xxxxx2'));
		
		echo json_encode($users);
	
		//CPlayer::model()->delete('starjiang5');
		$this->render('index/index.tpl');
	
	}
	
	public function dbAction()
	{
		var_dump(CDBUser::model()->setKey('1001')->setHead('adadads')->save());
		var_dump(CDBUser::model()->get('1001'));
	}
	
	public function sfdbAction()
	{
		var_dump(CSFDBUser::model()->setKey('1002ff')->setHead('ad')->save());
		var_dump(CSFDBUser::model()->get('1002'));
		
		var_dump(CSFDBUser::mget(array(1001,1002)));
	}
	
	public function lbdbAction()
	{
		var_dump(CLBDBUser::model()->setKey('1004')->setHead('ad')->save());
		var_dump(CLBDBUser::model()->get('1004'));
	
		var_dump(CLBDBUser::mget(array(1001,1002,1003,1004,1005)));
	}
		
	public function lbmongoAction()
	{
		var_dump(CLBMGUser::model()->setKey('1005')->setHead('ad')->save());
		//var_dump(CLBMGUser::model()->get('1004'));
	
		var_dump(CLBMGUser::mget(array(1001,1002,1003,'1004','1005')));
	}
	
	public function trAction()
	{
		//var_dump(CTRUser::model()->setKey('1004')->setHead('ad')->save());
		//var_dump(CTRUser::model()->get('1007'));
	
		var_dump(CTRUser::mget(array(1001,1002,1003,'1004','1005',1007)));
	}
}