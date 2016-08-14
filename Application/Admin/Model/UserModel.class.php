<?php
namespace Admin\Model;
use Think\Model, Think\Page, Org\Net\IpLocation;

class UserModel extends Model {
	protected $_validate = array(
		array('mobile','require','手机号不能为空'),
		array('mobile','','手机号已经存在', self::EXISTS_VALIDATE, 'unique', self::MODEL_BOTH),
		array('mobile','/^1(3[0-9]|4[57]|5[0-35-9]|8[0-9]|70)\d{8}$/','手机号格式不正确', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
		array('password','require','用户密码不能为空'),
		array('qq','require','QQ不能为空'),
		array('qq','number','QQ号格式不正确'),
	);

	protected $_auto = array(
		array('password', 'password_hash', self::MODEL_INSERT, 'function', array(PASSWORD_DEFAULT)), //password_hash加密
		array('register_time', 'date', self::MODEL_INSERT, 'function', array('Y-m-d H:i:s')), //注册时间
		array('register_ip', 'get_client_ip', self::MODEL_INSERT, 'function'), //注册IP
		array('last_ip', 'get_client_ip', self::MODEL_UPDATE, 'function'), //最后登录IP
	);

	protected function _before_insert(&$data,$options) {
		$data['email'] = empty($data['email']) ? $data['qq'] . '@qq.com' : $data['email'];
	}

	protected function _before_write(&$data) {
		//手机号归属地
		if (!empty($data['mobile'])) {
			$model = M('MobileArea');
			$mobile_number = substr($data['mobile'], 0, 7);
			$result = $model->where(compact('mobile_number'))->getField('mobile_area');
			$data['mobile_address'] = (string) $result;
		}
		//登录所在地
		if (!empty($data['last_ip'])) {
			$ip = new IpLocation();
			$area = $ip->getlocation($data['last_ip']);
			empty($area) or $data['login_address'] = $area['country'] . ' ' . $area['area'];
		}
	}

	public function search() {
		$list = 10;
		$where = array();
		if ($mobile = I('get.mobile'))
			$where['mobile'] = array('LIKE', "%{$mobile}%");
    	if ($qq = I('get.qq'))
			$where['qq'] = array('LIKE', "%{$qq}%");
    	if ($register_time = I('get.register_time'))
			$where['register_time'] = array('LIKE', "%{$register_time}%");
    	if ($mobile_address = I('get.mobile_address'))
			$where['mobile_address'] = array('LIKE', "%{$mobile_address}%");
    	if ($login_address = I('get.login_address'))
			$where['login_address'] = array('LIKE', "%{$login_address}%");
    	$total = $this->where($where)->count();
		$page = new Page($total,$list);
		$page->setConfig('first','首页');
		$page->lastSuffix = false;
		$page->setConfig('last','尾页');
		$page->setConfig('prev','上一页');
		$page->setConfig('next','下一页');
		$pageStr = $page->show();
		$data = $this->where($where)->limit($page->firstRow.','.$page->listRows)
				->order('user_id DESC')->select();
		return array(
			'page' => $pageStr,
			'data' => $data,
		);
	}
}