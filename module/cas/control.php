<?php
require_once 'CAS.php';

class cas extends control
{
    public function login()
    {
		// Enable debugging
		phpCAS::setDebug("/opt/lampp/logs/cas.log");

		// Initialize phpCAS
		// phpCAS::client(CAS_VERSION_2_0, $cas_host, $cas_port, $cas_context);
		phpCAS::client(CAS_VERSION_2_0, "cas.sso.skyflyer.cn", 443, "/cas");

		// For production use set the CA certificate that is the issuer of the cert
		// on the CAS server and uncomment the line below
		// phpCAS::setCasServerCACert($cas_server_ca_cert_path);

		// For quick testing you can disable SSL validation of the CAS server.
		// THIS SETTING IS NOT RECOMMENDED FOR PRODUCTION.
		// VALIDATING THE CAS SERVER IS CRUCIAL TO THE SECURITY OF THE CAS PROTOCOL!
		phpCAS::setNoCasServerValidation();

		// force CAS authentication
		phpCAS::forceAuthentication();
		
		// at this step, the user has been authenticated by the CAS server
		// and the user's login name can be read with phpCAS::getUser().

		// logout if desired
		if (isset($_REQUEST['logout'])) {
			phpCAS::logout();
		}

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
		}
		//-------------------------------------end-------------------------------------
    }

    public function logout()
    {
        phpCAS::logout();
    }
}
?>