# zentaopms
禅道开源版6.4 + cas client 1.3.4


## cas 对接修改内容

/module/cas/control.php

    // 通过修改的casLogin函数授权
		//-------------------------------------start-------------------------------------
        $account = phpCAS::getUser();
        echo($account);
		
		$location = "/zentaopms/www/index.php?m=index&f=index";
		
		// 获取用户信息
		$user = $this->loadModel('user')->getById($account);
		if($user)
		{
			$this->user->cleanLocked($user->account);
			/* Authorize him and save to session. */
			$user->rights = $this->user->authorize($account);
			$user->groups = $this->user->getGroups($account);
			$this->dao->update(TABLE_USER)->set('visits = visits + 1')->set('ip')->eq($userIP)->set('last')->eq($last)->where('account')->eq($user->account)->exec();
			
			$this->session->set('user', $user);
			$this->app->user = $this->session->user;
			$this->loadModel('action')->create('user', $user->id, 'login');
			
			if (isset($_REQUEST['referer'])) {
				$this->locate($_REQUEST['referer']);
			}
			else
			{
				// 跳转页面到index页面或者referer页面
				$this->locate($location);
			}
		}
		else
		{
			echo "用户".$account."不存在，请联系管理员申请用户。";
      //说明：cas登录后只有用户id，故需要提前在数据库中添加用户 or 此处修改为自动创建用户并登录跳转到首页。
		}
		//-------------------------------------end-------------------------------------
