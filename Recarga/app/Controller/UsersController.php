<?php

Class UsersController extends AppController{
	public $helpers = array('Html', 'Form');
	public $components = array('Flash', 'Session');

	public function index(){
		$params = array('order' => 'names desc');
		$this->set('usuarios', $this->User->find('list'),$params);
	} 

	public function add(){
		if($this->request->is('post')){
			if($this->User->save($this->request->data)){
				$this->Flash->set('Se Guardo!');
				$this->redirect(array('action' => 'index'));
			}
		}
	}

}