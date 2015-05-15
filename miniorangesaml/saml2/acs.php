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
	include 'Response.php';
	
	define('_JEXEC', 1);
	
	if (!defined('_JDEFINES')) {
		define('JPATH_BASE', dirname(dirname(dirname(dirname(dirname(__FILE__))))));
		require_once JPATH_BASE . '/includes/defines.php';
	}

	require_once JPATH_BASE . '/includes/framework.php';
	
	$miniOrangePlugin = JPluginHelper::getPlugin('authentication', 'miniorangesaml');
	
	if (!$miniOrangePlugin) {
        throw new Exception("miniOrange SAML Plugin not active");
    }
	
	jimport('joomla.html.parameter');
    $plgParams = new JRegistry();
	if ($miniOrangePlugin && isset($miniOrangePlugin->params)) {
        $plgParams->loadString($miniOrangePlugin->params);
    }
	
	if (array_key_exists('SAMLResponse', $_POST)) {
		$samlResponse = $_POST['SAMLResponse'];
	} else {
		throw new Exception('Missing SAMLRequest or SAMLResponse parameter.');
	}
	
	if(array_key_exists('RelayState', $_POST)) {
		$relayState = $_POST['RelayState'];
	} else {
		$relayState = '';
	}
	
	$samlResponse = base64_decode($samlResponse);
	$document = new DOMDocument();
	$document->loadXML($samlResponse);
	$samlResponseXml = $document->firstChild;
	
	$signatureData = Utilities::validateElement($samlResponseXml);
	if($signatureData !== FALSE) {
		$validSignature = Utilities::validateSignature($signatureData, $plgParams['miniorange_saml_idp_x509cert']);
		if($validSignature === FALSE) {
			throw new Exception('Invalid signature.');
		}
	}
	
	$samlResponse = new SAML2_Response($samlResponseXml);
	
	// verify the issuer and audience from saml response
	$acsUrl = JURI::base() . 'acs.php';
	$issuer = $plgParams['miniorange_saml_idp_entityid'];
	Utilities::validateIssuerAndAudience($samlResponse, $acsUrl, $issuer);
	
	$username = $samlResponse->getAssertions()[0]->getNameId()['Value'];
	//echo $username;
	//echo JURI::base();
	$siteUrl = substr(JURI::base(), 0, strpos(JURI::base(), '/plugins'));
	//echo '<br />' . $siteUrl;
	$urlToPost = $siteUrl . '/plugins/authentication/miniorangesaml/miniorangesaml.php';
	
	// set cookie for ssoemail
	setcookie('ssoemail', $username, time() + 60000, '/');

	header('Location: ' . $urlToPost);
	//echo $samlResponse->getAssertions()[0]->getCertificates()[0];
	//echo '<html><body>Please wait...<script>window.onload = function(){document.forms["saml-form"].submit();}</script><form id="saml-form" action="' . $urlToPost . '" method="post"><input type="hidden" name="username" value="' . $username . '"/></form></body></html>';	
}
?>