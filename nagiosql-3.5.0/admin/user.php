<?php
/* ----------------------------------------------------------------------------
 NagiosQL
-------------------------------------------------------------------------------
 (c) 2005-2023 by Martin Willisegger

 Project   : NagiosQL
 Component : User administration
 Website   : https://sourceforge.net/projects/nagiosql/
 Version   : 3.5.0
 GIT Repo  : https://gitlab.com/wizonet/NagiosQL
-----------------------------------------------------------------------------*/

use functions\MysqliDbClass;
use functions\NagContentClass;
use functions\NagDataClass;
use functions\NagVisualClass;

/**
 * Class and variable includes
 * @var HTML_Template_IT $conttp Content template
 * @var HTML_Template_IT $maintp Main template
 * @var HTML_Template_IT $mastertp Master template (list view)
 * @var MysqliDbClass $myDBClass MySQL database class
 * @var NagVisualClass $myVisClass Visual content class
 * @var NagDataClass $myDataClass NagiosQL data class
 * @var NagContentClass $myContentClass NagiosQL content class
 * @var string $setFileVersion from prepend_adm.php -> Application version string
 * @var int $chkActive from prepend_adm.php -> Active checkbox
 * @var string $chkModus from prepend_adm.php -> Form work mode
 * @var int $chkDataId from prepend_adm.php -> Actual dataset id
 * @var string $chkSelModify from prepend_adm.php -> Modification selection value
 * @var int $hidSortBy from prepend_adm.php -> Sort data by
 * @var string $hidSortDir from prepend_adm.php -> Sort data direction (ASC, DESC)
 * @var int $chkLimit from prepend_adm.php / settings -> Data set count per page
 * @var array $SETS Settings array
 * @var int $intGlobalWriteAccess from prepend_content.php -> Global admin write access
 * @var int $intWriteAccessId from prepend_content.php -> Admin write access to actual dataset id
 * @var string $chkTfValue1 from prepend_content.php -> User name
 * @var string $chkTfValue2 from prepend_content.php -> User description
 * @var string $chkTfValue3 from prepend_content.php -> Password
 * @var string $chkTfValue4 from prepend_content.php -> Password confirmation
 * @var string $chkTfValue5 from prepend_content.php -> (hidden) old user name
 * @var int $chkChbValue1 from prepend_content.php -> Admin rights checkbox
 * @var int $chkChbValue2 from prepend_content.php -> Webserver authentification checkbox
 * @var int $chkSelValue1 from prepend_content.php -> Language selector
 * @var int $chkSelValue2 from prepend_content.php -> Standard domain selector
 */
/*
Path settings
*/
$strPattern = '(admin/[^/]*.php)';
$preRelPath = preg_replace($strPattern, '', filter_input(INPUT_SERVER, 'PHP_SELF'));
$preBasePath = preg_replace($strPattern, '', filter_input(INPUT_SERVER, 'SCRIPT_FILENAME'));
/*
Define common variables
*/
$prePageId = 32;
$preContent = 'admin/user.htm.tpl';
$preListTpl = 'admin/datalist_common.htm.tpl';
$preSearchSession = 'user';
$preTableName = 'tbl_user';
$preKeyField = 'username';
$preAccess = 1;
$preFieldvars = 1;
$preNoAccessGrp = 1;
$strErrorMessage = '';
$strInfoMessage = '';
$strConsistMessage = '';
/*
Include preprocessing file
*/
require $preBasePath . 'functions/prepend_adm.php';
require $preBasePath . 'functions/prepend_content.php';
/*
Add or modify data
*/
if ((($chkModus === 'insert') || ($chkModus === 'modify')) && ($intGlobalWriteAccess === 0)) {
    /* Check password */
    if ((($chkTfValue3 === $chkTfValue4) && (strlen($chkTfValue3) > 5)) ||
        (($chkModus === 'modify') && ($chkTfValue3 === ''))) {
        if ($chkTfValue3 === '') {
            $strPasswd = '';
        } else {
            $strPasswd = "`password`=MD5('$chkTfValue3'),";
        }
        /* Admin user cannot be renamed and must be active with full admin rights */
        if (strtolower($chkTfValue5) === 'admin') {
            $chkTfValue1 = 'admin';
            $chkActive = '1';
            $chkChbValue1 = '1';
        }
        $strSQLx = "`$preTableName` SET `$preKeyField`='$chkTfValue1', `alias`='$chkTfValue2', $strPasswd "
            . "`admin_enable`='$chkChbValue1', `wsauth`='$chkChbValue2', `active`='$chkActive', "
            . "`language`='$chkSelValue1', `domain`='$chkSelValue2', `last_modified`=NOW()";
        if ($chkModus === 'insert') {
            $strSQL = 'INSERT INTO ' . $strSQLx;
        } else {
            $strSQL = 'UPDATE ' . $strSQLx . ' WHERE `id`=' . $chkDataId;
        }
        if ($intWriteAccessId === 0) {
            if (($chkTfValue1 !== '') && ($chkTfValue2 !== '')) {
                $intReturn = $myDataClass->dataInsert($strSQL, $intInsertId);
                if ($intReturn === 1) {
                    $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                } else {
                    $myVisClass->processMessage($myDataClass->strInfoMessage, $strInfoMessage);
                    if ($chkModus === 'insert') {
                        $myDataClass->writeLog(translate('New user added:') . ' ' . $chkTfValue1);
                    }
                    if ($chkModus === 'modify') {
                        $myDataClass->writeLog(translate('User modified:') . ' ' . $chkTfValue1);
                    }
                }
            } else {
                $myVisClass->processMessage(
                    translate('Database entry failed! Not all necessary data filled in!'),
                    $strErrorMessage
                );
            }
        } else {
            $myVisClass->processMessage(translate('Database entry failed! No write access!'), $strErrorMessage);
        }
    } else {
        $myVisClass->processMessage(translate('Password too short or password fields do not match!'), $strErrorMessage);
    }
    $chkModus = 'display';
}
if ($chkModus !== 'add') {
    $chkModus = 'display';
}
/*
Singe data form
*/
if ($chkModus === 'add') {
    /* Process domain selection field */
    $intFieldId = $arrModifyData['domain'] ?? 1;
    $intReturn1 = $myVisClass->parseSelectSimple('tbl_datadomain', 'domain', 'std_domain', 0, $intFieldId, 0);
    if ($intReturn1 !== 0) {
        $myVisClass->processMessage($myVisClass->strErrorMessage, $strErrorMessage);
    }
    /* Process language selection field */
    $intFieldId = $arrModifyData['language'] ?? 0;
    if ($intFieldId === 0) {
        $intFieldId = $myDBClass->getFieldData('SELECT `id` FROM `tbl_language` '
            . "WHERE `locale`='" . $_SESSION['SETS']['data']['locale'] . "'");
        $intFieldId = (int)$intFieldId;
    }
    $intReturn2 = $myVisClass->parseSelectSimple('tbl_language', 'language', 'language_name', 0, $intFieldId);
    if ($intReturn2 !== 0) {
        $myVisClass->processMessage($myVisClass->strErrorMessage, $strErrorMessage);
    }
    /* Initial add/modify form definitions */
    $myContentClass->addFormInit($conttp);
    $conttp->setVariable('TITLE', translate('User administration'));
    $conttp->setVariable('WSAUTH_DISABLE', 'disabled');
    $conttp->setVariable('FILL_ALLFIELDS', translate('Please fill in all fields marked with an *'));
    $conttp->setVariable('FILL_ILLEGALCHARS', translate('The following field contains illegal characters:'));
    $conttp->setVariable('FILL_PASSWD_NOT_EQUAL', translate('The passwords do not match!'));
    $conttp->setVariable('FILL_PASSWORD', translate('Please fill in the password'));
    $conttp->setVariable('FILL_PWDSHORT', translate('The password is too short - use at least 6 characters!'));
    $conttp->setVariable('LANG_WEBSERVER_AUTH', translate('Webserver authentification'));
    $conttp->setVariable('PASSWORD_MUST', 'class="inpmust"');
    $conttp->setVariable('PASSWORD_MUST_STAR', '*');
    /* If webserver authetification is enabled - show option field */
    if (isset($SETS['security']['wsauth']) && ((int)$SETS['security']['wsauth'] === 1)) {
        $conttp->setVariable('WSAUTH_DISABLE');
    }
    /* Insert data from database in "modify" mode */
    if (isset($arrModifyData) && ($chkSelModify === 'modify')) {
        /* Process data */
        $myContentClass->addInsertData($conttp, $arrModifyData, 0, '');
        /* Webserver authentification */
        $conttp->setVariable('WSAUTH_CHECKED');
        if ((int)$arrModifyData['wsauth'] === 1) {
            $conttp->setVariable('WSAUTH_CHECKED', 'checked');
        }
        /* Object based group administration */
        $conttp->setVariable('ADMINENABLE_CHECKED');
        if ((int)$arrModifyData['admin_enable'] === 1) {
            $conttp->setVariable('ADMINENABLE_CHECKED', 'checked');
        }
        /* Admin rules */
        if ((string)$arrModifyData[$preKeyField] === 'Admin') {
            $conttp->setVariable('NAME_DISABLE', 'disabled');
            $conttp->setVariable('ACT_DISABLE', 'disabled');
            $conttp->setVariable('WSAUTH_DISABLE', 'disabled');
            $conttp->setVariable('ADMINENABLE_DISABLE', 'disabled');
            $conttp->setVariable('ADMINENABLE_CHECKED', 'checked');
        }
        $conttp->setVariable('PASSWORD_MUST');
        $conttp->setVariable('PASSWORD_MUST_STAR');
    }
    $conttp->parse('datainsert');
    $conttp->show('datainsert');
}
/*
Data list view
*/
if ($chkModus === 'display') {
    /* Initial list view definitions */
    $myContentClass->listViewInit($mastertp);
    $mastertp->setVariable('TITLE', translate('User administration'));
    $mastertp->setVariable('FIELD_1', translate('Username'));
    $mastertp->setVariable('FIELD_2', translate('Description'));
    /* Row sorting */
    $strOrderString = "ORDER BY `$preKeyField` $hidSortDir";
    if ($hidSortBy === 2) {
        $strOrderString = "ORDER BY `alias` $hidSortDir";
    }
    /* Count datasets */
    $intLineCount = 0;
    /** @noinspection SqlResolve */
    $strSQL = "SELECT count(*) AS `number` FROM `$preTableName`";
    $booReturn1 = $myDBClass->hasSingleDataset($strSQL, $arrDataLinesCount);
    if ($booReturn1 === false) {
        $myVisClass->processMessage(translate('Error while selecting data from database:'), $strErrorMessage);
        $myVisClass->processMessage($myDBClass->strErrorMessage, $strErrorMessage);
    } else {
        $intLineCount = (int)$arrDataLinesCount['number'];
        if ($intLineCount < $chkLimit) {
            $chkLimit = 0;
        }
    }
    /* Get datasets */
    $strSQL = "SELECT `id`, `$preKeyField`, `alias`, `active`, `nodelete` "
        . "FROM `$preTableName` $strOrderString LIMIT $chkLimit," . $SETS['common']['pagelines'];
    $booReturn2 = $myDBClass->hasDataArray($strSQL, $arrDataLines, $intDataCount);
    if ($booReturn2 === false) {
        $myVisClass->processMessage(translate('Error while selecting data from database:'), $strErrorMessage);
        $myVisClass->processMessage($myDBClass->strErrorMessage, $strErrorMessage);
    }
    /* Process data */
    $myContentClass->listData($mastertp, $arrDataLines, $intDataCount, $intLineCount, $preKeyField, 'alias');
}
/* Show messages */
$myContentClass->showMessages($mastertp, $strErrorMessage, $strInfoMessage, $strConsistMessage, array(), '', 1);
/*
Process footer
*/
$myContentClass->showFooter($maintp, $setFileVersion);