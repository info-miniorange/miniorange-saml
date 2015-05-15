<?php
/**
 * @package    miniOrange
 * @author	   miniOrange Security Software Pvt. Ltd.
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
	include 'Utilities.php';
	define('_JEXEC', 1);
	

	if (!defined('_JDEFINES')) {
		define('JPATH_BASE', dirname(dirname(dirname(dirname(dirname(__FILE__))))));
		require_once JPATH_BASE . '/includes/defines.php';
	}

	require_once JPATH_BASE . '/includes/framework.php';
	//echo JURI::base();
	//$siteUrl = substr(JURI::base(), 0, strpos(JURI::base(), '/plugins'));
	
	$miniOrangePlugin = JPluginHelper::getPlugin('authentication', 'miniorangesaml');
	
	if (!$miniOrangePlugin) {
        throw new Exception("miniOrange SAML Plugin not active");
    }
	
	jimport('joomla.html.parameter');
    $plgParams = new JRegistry();
	if ($miniOrangePlugin && isset($miniOrangePlugin->params)) {
        $plgParams->loadString($miniOrangePlugin->params);
    }
	
	$acsUrl = JURI::base() . 'acs.php';
	$issuer = 'miniorange-joomla-authentication-plugin';
	$ssoUrl = $plgParams['miniorange_saml_idp_sso'];
	$samlRequest = Utilities::createAuthnRequest($acsUrl, $issuer);
	//echo $samlRequest;
	$redirect = $ssoUrl . '?SAMLRequest=' . $samlRequest;
	header('Location: '.$redirect);
}
?>