<?php
/**
 * @package    miniOrange
 * @subpackage Plugins
 * @license    GNU/GPLv3
 * @copyright  Copyright 2015 miniOrange. All Rights Reserved.
 *
 *
 * This file is part of miniOrange SAML plugin.
 *
 * miniOrange SAML plugin is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * miniOrange SAML plugin is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with miniOrange SAML plugin.  If not, see <http://www.gnu.org/licenses/>.
 */
 
if (!defined('_JEXEC')) {
	/**
     * Constant that is checked in included files to prevent direct access.
     * define() is used in the installation folder rather than "const" to not error for PHP 5.2 and lower
     */
    define('_JEXEC', 1);
	

	if (!defined('_JDEFINES')) {
		define('JPATH_BASE', dirname(dirname(dirname(dirname(__FILE__)))));
		require_once JPATH_BASE . '/includes/defines.php';
	}

	require_once JPATH_BASE . '/includes/framework.php';
	
	// Instantiate the application.
    $app = JFactory::getApplication('site');
    $app->initialise();
	$login_url = JRoute::_('../../../index.php/component/users/profile', true);
	
	$miniOrangePlugin = JPluginHelper::getPlugin('authentication', 'miniorangesaml');
	
	if (!$miniOrangePlugin) {
        throw new Exception("miniOrange SAML Plugin not active");
    }
	
	$user =& JFactory::getUser(); #Get current user info
	$session =& JFactory::getSession(); #Get current session vars
	
	jimport('joomla.html.parameter');
    $plgParams = new JRegistry();
	if ($miniOrangePlugin && isset($miniOrangePlugin->params)) {
        $plgParams->loadString($miniOrangePlugin->params);
    }
	
	//jimport('miniorange.initialize');
	//$saml_auth = miniorange_saml_instance($plgParams);

	
	// get site url from base path
	$siteUrl = substr(JURI::base(), 0, strpos(JURI::base(), '/plugins'));
	
	/*if (!isset($_GET['username'])) {
		
		$params = array(
			'ErrorURL' => $siteUrl. "/libraries/miniorange/saml/module.php/core/authenticate.php?as=miniorange-sp",
			'ReturnTo' => $siteUrl. "/libraries/miniorange/saml/module.php/core/authenticate.php?as=miniorange-sp",
		);
		
		$saml_auth->login($params);
	}*/
	/*if(!isset($_POST['username'])) {
		$url = $siteUrl . '/plugins/authentication/miniorangesaml/saml2/AuthnRequest.php';
		header('Location: ' . $url);
	}*/
		
	if(!isset($_COOKIE['ssoemail'])) {
		$url = $siteUrl . '/plugins/authentication/miniorangesaml/saml2/AuthnRequest.php';
		header('Location: ' . $url);
	}
	else if (isset($_COOKIE['ssoemail'])) {
		$ssoemail = $_COOKIE['ssoemail'];
		
		// Unset cookie for username
		setcookie('ssoemail', '', time() - 100, '/');
		
		//Check if email is exist in database
		$db = &JFactory::getDBO();
		$db->setQuery('SELECT * FROM #__users where email=\''.$ssoemail.'\'');
		$userRow = $db->loadObject();
		
		if($userRow->id) {
			$uid = $userRow->id;
			$jUser = JFactory::getUser($uid);
			
			$instance = $jUser;     
            $instance->set('guest', 0);

            // Register the needed session variables
            $session->set('user',$jUser);

            // Check to see the the session already exists.                        
            $app->checkSession();
			
			// Hit the user last visit field
			$instance->setLastVisit();
			
			$app->redirect($login_url);
		} else {   
		
			$app->redirect($login_url,'We did not find your email address in our system. Please register.');
		}
	}
	
} else {
	
	/**
	 * Example Authentication Plugin.  Based on the example.php plugin in the Joomla! Core installation
	 *
	 * @package    Joomla.Tutorials
	 * @subpackage Plugins
	 * @license    GNU/GPL
	 */
	class plg_authentication_miniorangesaml extends JPlugin
	{
		/**
		 * This method should handle any authentication and report back to the subject
		 * This example uses simple authentication - it checks if the password is the reverse
		 * of the username (and the user exists in the database).
		 *
		 * @access    public
		 * @param     array     $credentials    Array holding the user credentials ('username' and 'password')
		 * @param     array     $options        Array of extra options
		 * @param     object    $response       Authentication response object
		 * @return    boolean
		 * @since 1.5
		 */
		function onUserAuthenticate( $credentials, $options, &$response )
		{
			/*
			 * Here you would do whatever you need for an authentication routine with the credentials
			 *
			 * In this example the mixed variable $return would be set to false
			 * if the authentication routine fails or an integer userid of the authenticated
			 * user if the routine passes
			 */
		$query	= $this->db->getQuery(true)
			->select('id')
			->from('#__users')
			->where('username=' . $db->quote($credentials['username']));
	 
		$this->db->setQuery($query);
		$result = $this->db->loadResult();
	 
		if (!$result) {
			$response->status = STATUS_FAILURE;
			$response->error_message = 'User does not exist';
		}
	 
		/**
		 * To authenticate, the username must exist in the database, and the password should be equal
		 * to the reverse of the username (so user joeblow would have password wolbeoj)
		 */
		if($result && ($credentials['username'] == strrev( $credentials['password'] )))
		{
			$email = JUser::getInstance($result); // Bring this in line with the rest of the system
			$response->email = $email->email;
			$response->status = JAuthentication::STATUS_SUCCESS;
		}
		else
		{
			$response->status = JAuthentication::STATUS_FAILURE;
			$response->error_message = 'Invalid username and password';
		}
		}
	}
}
?>