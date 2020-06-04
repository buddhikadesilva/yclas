<?php defined('SYSPATH') or die('No direct script access.');

/**
 * 
 * Display spam pool of user profiles
 * 
 */
class Controller_Panel_Pool extends Auth_Controller {
	
	public function action_index()
	{
		//template header
		$this->template->title           	= __('Black list');
		$this->template->meta_description	= __('Black list');
		Breadcrumbs::add(Breadcrumb::factory()->set_title(__('List')));

		//find all tables 
		
		$user = new Model_User();
		$black_list = $user->where('status', '=', Model_User::STATUS_SPAM)
						   ->order_by('id_user')
						   ->find_all();

		$this->template->content = View::factory('oc-panel/pages/black_list', array('black_list' => $black_list,));
	}

	public function action_remove()
	{

		if($id = $this->request->param('id'))
		{
			$user = new Model_User($id);
			if($user->loaded())
			{
				$user->status = Model_User::STATUS_ACTIVE;
				try {
					$user->save();
					Alert::set(Alert::SUCCESS, sprintf(__('User %s has been removed from black list.'),$user->name));
					$this->redirect(Route::url('oc-panel', array('controller'=>'pool','action'=>'index')));
				} catch (Exception $e){}
			}
			$this->redirect(Route::url('oc-panel', array('controller'=>'pool','action'=>'index')));
		}
	}
}
