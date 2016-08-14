<?php
namespace Mobile\Controller;
use Think\Controller, Admin\Model\UserModel;

class PublicController extends Controller {
	public function register() {
		$userModel = new UserModel();
		if ($userModel->create()) {
			if ($userModel->add()) {
				echo jsonMess(0, '注册成功');
			} else {
				echo jsonMess(1, '注册失败');
			}
		} else {
			echo jsonMess(1, $userModel->getError());
		}
	}

	public function login() {
		try {
			$mobile = I('post.mobile', '');
			$password = I('post.password', '');
			$userModel = new UserModel();
			$userInfo = $userModel->field(array('user_id', 'password'))->where(compact('mobile'))->find();
			if (empty($userInfo['password'])) {
				throw new \Exception('用户不存在');
			}
			if (password_verify($password, $userInfo['password'])) {
				$_SESSION['user_id'] = $userInfo['user_id'];
				echo jsonMess(0, '登录成功');
			} else {
				throw new \Exception('密码错误');
			}
		} catch (\Exception $e) {
			echo jsonMess(1, $e->getMessage());
		}
	}
}