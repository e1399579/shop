<?php
namespace Admin\Controller;
use Admin\Controller\AuthController;

class UserController extends AuthController {
	public function index() {
		$model = D('User');
		$data = $model->search();
		$this->assign($data);
		$map = array(
			'module' => MODULE_NAME,
			'controller' => CONTROLLER_NAME,
			'action' => ACTION_NAME,
		);
		$menu = M('Menu')->where($map)->order('menu_id DESC')->find();
		$this->assign('menu', $menu);
		$this->display();
	}
	
	public function search() {
		$model = D('User');
		$data = $model->search();
		echo json_encode($data);
	}
	
	public function add(){
		$this->display();
	}
	
	public function addPost() {
		$model = D('User');
		if ($model->create()) {
			if ($model->add()) {
				$this->success('添加成功', U('index'));
				return;
			} else {
				$this->error('添加失败');
			}
		} else {
			$this->error($model->getError());
		}
	}
	
	public function del($id) {
		$model = D('User');
		$model->delete($id);
		$this->success('删除成功!');
		return;
	}
	
	public function bdel(){
		$delid = I('post.delid');
		if ($delid) {
			$did = implode(',', $delid);
			$model = D('User');
			$model->delete($did);
		}
		$this->success('删除成功！');
		return;
	}
	
	public function save($id){
		$model = M('User');
		$info = $model->find($id);
		$this->assign('info', $info);
		$this->display();
	}
	
	public function savePost(){
		$model = D('User');
		if ($model->create()) {
			if ($model->save() !== false) {
				$this->success('修改成功', U('index'));
				return;
			} else {
				$this->error('修改失败');
			}
		} else {
			$this->error($model->getError());
		}
	}
}