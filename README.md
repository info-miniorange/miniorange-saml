
miniOrange Joomla SSO Plugin
============================

Overview
--------

The miniOrange SAML2 Joomla plugin provides Single Sign-On to Joomla users into their account. This document is a step by step guide to configure SSO for Joomla using miniOrange SAML2 plugin.

In order to setup SSO, you need to follow these 5 steps:

 * Configure Single Sign-On Settings in miniOrange
 * Create a policy for Joomla in miniOrange
 * Download the plugin from miniOrange and Install it in Joomla
 * Configuring the plugin
 * Adding SAML login link in Joomla

Configure Single Sign-On Settings in miniOrange
-----------------------------------------------

 * Login as a customer from Admin Console of miniOrange's Administrator Console, now go to Apps tab from menu and select Configure Apps.
 * Select the Application Name Joomla from the drop down menu.
 * Make sure the ACS URL is <path-to-joomla-site>/plugins/authentication/miniorangesaml/saml2/acs.php.
 * Click on Save to configure Joomla.
 * Download the certificate which you will need later while configuring the plugin.

Create a policy for Joomla in miniOrange
----------------------------------------
 * Login as a customer from Admin Console of miniOrange's Administrator Console, go to Policies tab from menu and select App Authentication Policy.
 * Add a new policy for Joomla.

Download the plugin from miniOrange and Install it in Joomla
------------------------------------------------------------

 * You can download the plugin by logging in as a customer from Admin Console of miniOrange's Administrator Console and clicking on Guide to Secure Joomla from Dashboard.
 * Login as administrator in Joomla.
 * Click on Extension Manager under Extensions.
 * Choose miniOrange Joomla plugin file to install (i.e. miniorangesaml.zip).
 * Install the plugin.

Configuring the plugin
----------------------

 * In the Joomla admin interface, click on Plugin Manager under Extensions.
 * Search for miniorange in plugins.
 * Click on the plugin name and go to Identity Provider Settings tab.
 * Provide the required settings (i.e. Entity ID, Single SignOn Service Url, X.509 certificate) and save it.
 * Once the settings are done enable the plugin from the Plugin Manager.

Adding SAML login link in Joomla
--------------------------------

The SAML login link can be added to Joomla main login form as follows:
 * Login as administrator in Joomla.
 * Click on Template Manager under Extensions.
 * Click on Templates in the sidebar.
 * Select the site template that is currently being used (for example: Protostar).
 * Now select default_login.php under html->com_users->login.
 * Search for the JLOGIN button in default_login.php.
 * After this button, add the SAML Login link by adding code:
 ```
 <a href="http://<path-to-joomla-site>/plugins/authentication/miniorangesaml/miniorangesaml.php" style="padding-left:20px;">SAML Login</a>
 ```
