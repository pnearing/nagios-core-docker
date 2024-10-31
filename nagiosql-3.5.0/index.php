<?php
/* ----------------------------------------------------------------------------
 NagiosQL
-------------------------------------------------------------------------------
 (c) 2005-2022 by Martin Willisegger

 Project   : NagiosQL
 Component : Start script
 Website   : https://sourceforge.net/projects/nagiosql/
 Version   : 3.5.0
 GIT Repo  : https://gitlab.com/wizonet/NagiosQL
-----------------------------------------------------------------------------*/
/**
 * Class and variable includes
 * @var HTML_Template_IT $conttp
 * @var HTML_Template_IT $maintp
 * @var string $setFileVersion from prepend_adm.php -> Application version string
 */
/*
Path settings
*/
$preRelPath = strstr(filter_input(INPUT_SERVER, 'PHP_SELF'), 'index.php', true);
$preBasePath = strstr(filter_input(INPUT_SERVER, 'SCRIPT_FILENAME'), 'index.php', true);
/*
Destroy old session data
*/
session_start();
session_destroy();
/*
Define common variables
*/
$intPageID = 0;
$preContent = 'index.htm.tpl';
/*
Redirect to installation wizard
*/
if (PHP_VERSION_ID < 70200) {
    header('Location: install/index.php');
}
/*
Include preprocessing file
*/
$preAccess = 0;
$preFieldvars = 0;
require 'functions/prepend_adm.php';
/*
Include Content
*/
$conttp->setVariable('TITLE', translate('Welcome to'));
$conttp->setVariable('TITLE_LOGIN', translate('Welcome'));
$conttp->setVariable('LOGIN_TEXT', translate('Please enter your username and password to access NagiosQL.<br>If '
    . 'you forgot one of them, please contact your Administrator.'));
$conttp->setVariable('USERNAME', translate('Username'));
$conttp->setVariable('PASSWORD', translate('Password'));
$conttp->setVariable('LOGIN', translate('Login'));
if (isset($_SESSION['strLoginMessage']) && ($_SESSION['strLoginMessage'] !== '')) {
    $conttp->setVariable('MESSAGE', $_SESSION['strLoginMessage']);
} else {
    $conttp->setVariable('MESSAGE', '&nbsp;');
}
$conttp->setVariable('ACTION_INSERT', filter_input(INPUT_SERVER, 'PHP_SELF'));
$conttp->setVariable('IMAGE_PATH', 'images/');
$conttp->parse('main');
$conttp->show('main');
/*
Include footer
*/
$maintp->setVariable('VERSION_INFO', "<a href='https://sourceforge.net/projects/nagiosql/' "
    . "target='_blank'>NagiosQL</a> $setFileVersion");
$maintp->parse('footer');
$maintp->show('footer');