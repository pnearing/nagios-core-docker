<?php
/* ----------------------------------------------------------------------------
 NagiosQL
-------------------------------------------------------------------------------
 (c) 2005-2023 by Martin Willisegger

 Project   : NagiosQL
 Component : Admin configuration target administration
 Website   : https://sourceforge.net/projects/nagiosql/
 Version   : 3.5.0
 GIT Repo  : https://gitlab.com/wizonet/NagiosQL
-----------------------------------------------------------------------------*/

use functions\MysqliDbClass;
use functions\NagConfigClass;
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
 * @var NagConfigClass $myConfigClass NagiosQL configuration class
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
 * @var string $strAccess from prepend_content.php -> List of read access group id's for actual user
 * @var string $chkTfValue1 from prepend_content.php -> Configuration target name
 * @var string $chkTfValue2 from prepend_content.php -> Configuration target description
 * @var string $chkTfValue4 from prepend_content.php -> Server name
 * @var string $chkTfValue5 from prepend_content.php -> User name
 * @var string $chkTfValue6 from prepend_content.php -> Password
 * @var string $chkTfValue7 from prepend_content.php -> ssh key file path
 * @var string $chkTfValue8 from prepend_content.php -> Base directory
 * @var string $chkTfValue9 from prepend_content.php -> Host configuration directory
 * @var string $chkTfValue10 from prepend_content.php -> Service configuration directory
 * @var string $chkTfValue11 from prepend_content.php -> Backup directory
 * @var string $chkTfValue12 from prepend_content.php -> Host backup directory
 * @var string $chkTfValue13 from prepend_content.php -> Service backup directory
 * @var string $chkTfValue14 from prepend_content.php -> Nagios base directory
 * @var string $chkTfValue15 from prepend_content.php -> Import directory
 * @var string $chkTfValue16 from prepend_content.php -> Picture directory
 * @var string $chkTfValue17 from prepend_content.php -> Command file
 * @var string $chkTfValue18 from prepend_content.php -> Binary file
 * @var string $chkTfValue19 from prepend_content.php -> Nagios PID file
 * @var string $chkTfValue20 from prepend_content.php -> Nagios configuration file
 * @var string $chkTfValue21 from prepend_content.php -> CGI configuration file
 * @var string $chkTfValue22 from prepend_content.php -> Ressource file
 * @var string $chkTfValue23 from prepend_content.php -> ssh port
 * @var int $chkSelValue1 from prepend_content.php -> Configuration access method (file based, ssh, ftp)
 * @var int $chkSelValue2 from prepend_content.php -> Nagios version
 * @var int $chkChbValue1 from prepend_content.php -> Use secure ftp
 * @var int $chkSelAccGr from prepend_content.php -> Access group selector
 */


$strPattern = '(admin/[^/]*.php)';
$preRelPath = preg_replace($strPattern, '', filter_input(INPUT_SERVER, 'PHP_SELF'));
$preBasePath = preg_replace($strPattern, '', filter_input(INPUT_SERVER, 'SCRIPT_FILENAME'));
/*
Define common variables
*/
$prePageId = 36;
$preContent = 'admin/configtargets.htm.tpl';
$preListTpl = 'admin/datalist_common.htm.tpl';
$preTableName = 'tbl_configtarget';
$preKeyField = 'target';
$preAccess = 1;
$preFieldvars = 1;
$intIsError = 0;
$strPathMessage = '';
$strErrorMessage = '';
$strInfoMessage = '';
$strConsistMessage = '';
/*
Include preprocessing file
*/
require $preBasePath . 'functions/prepend_adm.php';
require $preBasePath . 'functions/prepend_content.php';
/*
Process path values (add slashes)
*/
$chkTfValue8 = $myVisClass->addSlash($chkTfValue8);
$chkTfValue9 = $myVisClass->addSlash($chkTfValue9);
$chkTfValue10 = $myVisClass->addSlash($chkTfValue10);
$chkTfValue11 = $myVisClass->addSlash($chkTfValue11);
$chkTfValue12 = $myVisClass->addSlash($chkTfValue12);
$chkTfValue13 = $myVisClass->addSlash($chkTfValue13);
$chkTfValue14 = $myVisClass->addSlash($chkTfValue14);
$chkTfValue15 = $myVisClass->addSlash($chkTfValue15);
$chkTfValue16 = $myVisClass->addSlash($chkTfValue16);
/*
Check Port Value
*/
if ((int)$chkTfValue23 === 0) {
    $chkTfValue23 = 22;
}
/*
Check the permissions and other parameters
*/
if (($chkModus === 'modify' || $chkModus === 'insert') && $chkDataId !== 0) {
    if ($chkSelValue1 === 1) {
        $arrPaths = array($chkTfValue8, $chkTfValue9, $chkTfValue10, $chkTfValue11, $chkTfValue12, $chkTfValue13);
        foreach ($arrPaths as $elem) {
            if ($myConfigClass->isDirWriteable($elem) === 1) {
                $myVisClass->processMessage($elem . ' ' . translate('is not writeable'), $strPathMessage);
                $intIsError = 1;
            }
        }
        /* Nagios base configuration files */
        if (!is_writable($chkTfValue20)) {
            $myVisClass->processMessage(str_replace('  ', ' ', translate('Nagios config file') . ' ' . $chkTfValue20
                . ' ' . translate('is not writeable')), $strPathMessage);
            $intIsError = 1;
        } else {
            $intCheck = 0;
            if (file_exists($chkTfValue20) && is_readable($chkTfValue20)) {
                $resFile = fopen($chkTfValue20, 'rb');
                while (!feof($resFile)) {
                    $strLine = trim(fgets($resFile));
                    if ((substr_count($strLine, 'cfg_dir') !== 0) || (substr_count($strLine, 'cfg_file') !== 0)) {
                        $intCheck = 1;
                    }
                }
                fclose($resFile);
            }
            if ($intCheck === 0) {
                $myVisClass->processMessage(str_replace('  ', ' ', translate('Nagios config file') . ' ' .
                    $chkTfValue20 . ' ' . translate('is not a valid configuration file!')), $strPathMessage);
                $intIsError = 1;
            }
        }
        if (!is_writable($chkTfValue14)) {
            $myVisClass->processMessage(str_replace('  ', ' ', translate('Nagios base directory') . ' ' .
                $chkTfValue14 . ' ' . translate('is not writeable')), $strPathMessage);
            $intIsError = 1;
        }
        if (!is_writable($chkTfValue21)) {
            $myVisClass->processMessage(str_replace('  ', ' ', translate('Nagios cgi config file') . ' ' .
                $chkTfValue21 . ' ' . translate('is not writeable')), $strPathMessage);
            $intIsError = 1;
        }
        if (!is_readable($chkTfValue22)) {
            $myVisClass->processMessage(str_replace('  ', ' ', translate('Nagios resource config file') . ' ' .
                $chkTfValue22 . ' ' . translate('is not readable')), $strPathMessage);
            $intIsError = 1;
        }
    }
    /* Check SSH Method */
    if (($chkSelValue1 === 3) && !function_exists('ssh2_connect')) {
        $myVisClass->processMessage(translate('SSH module not loaded!'), $strPathMessage);
        $intIsError = 1;
    }
    /* Check FTP Method */
    if (($chkSelValue1 === 2) && !function_exists('ftp_connect')) {
        $myVisClass->processMessage(translate('FTP module not loaded!'), $strPathMessage);
        $intIsError = 1;
    }
    if ($intIsError === 1) {
        $chkModus = 'add';
        $chkSelModify = 'errormodify';
    }
}
/*
Add or modify data
*/
if ((($chkModus === 'insert') || ($chkModus === 'modify')) && ($intGlobalWriteAccess === 0)) {
    $strSQLx = "`$preTableName` SET `$preKeyField`='$chkTfValue1', `alias`='$chkTfValue2', `server`='$chkTfValue4', "
        . "`port`='$chkTfValue23', `method`='$chkSelValue1', `user`='$chkTfValue5', `password`='$chkTfValue6', "
        . "`ssh_key_path`='$chkTfValue7', `ftp_secure`=$chkChbValue1, `basedir`='$chkTfValue8', "
        . "`hostconfig`='$chkTfValue9', `serviceconfig`='$chkTfValue10', `backupdir`='$chkTfValue11', "
        . "`hostbackup`='$chkTfValue12', `servicebackup`='$chkTfValue13', `nagiosbasedir`='$chkTfValue14', "
        . "`importdir`='$chkTfValue15', `picturedir`='$chkTfValue16', `commandfile`='$chkTfValue17', "
        . "`binaryfile`='$chkTfValue18', `pidfile`='$chkTfValue19', `conffile`='$chkTfValue20', "
        . "`cgifile`='$chkTfValue21', `resourcefile`='$chkTfValue22',`version`=$chkSelValue2, "
        . "`access_group`=$chkSelAccGr, `active`='$chkActive',`last_modified`=NOW()";
    if ($chkModus === 'insert') {
        $strSQL = 'INSERT INTO ' . $strSQLx;
    } else {
        $strSQL = 'UPDATE ' . $strSQLx . ' WHERE `id`=' . $chkDataId;
    }
    if ($intWriteAccessId === 0) {
        if (($chkTfValue1 !== '') && ($chkTfValue2 !== '') && (($chkTfValue4 !== '') || ($chkDataId === 0))) {
            $intReturn = $myDataClass->dataInsert($strSQL, $intInsertId);
            if ($intReturn === 1) {
                $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
            } else {
                $myVisClass->processMessage($myDataClass->strInfoMessage, $strInfoMessage);
                if ($chkModus === 'insert') {
                    $myDataClass->writeLog(translate('New Domain inserted:') . ' ' . $chkTfValue1);
                }
                if ($chkModus === 'modify') {
                    $myDataClass->writeLog(translate('Domain modified:') . ' ' . $chkTfValue1);
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
    $chkModus = 'display';
}
if ($chkModus !== 'add') {
    $chkModus = 'display';
}
/*
Single view
*/
if ($chkModus === 'add') {
    /* Process acces group selection field */
    $intFieldId = $arrModifyData['access_group'] ?? 0;
    $intReturn = $myVisClass->parseSelectSimple('tbl_group', 'groupname', 'acc_group', 0, $intFieldId);
    if ($intReturn !== 0) {
        $myVisClass->processMessage($myVisClass->strErrorMessage, $strErrorMessage);
    }
    /* Initial add/modify form definitions */
    $myContentClass->addFormInit($conttp);
    $conttp->setVariable('TITLE', translate('Configuration domain administration'));
    if ($intIsError === 1) {
        $conttp->setVariable('PATHMESSAGE', '<h2 style="padding-bottom:5px;">' . translate('Warning, at least one ' .
                'error occured, please check!') . '</h2>' . $strPathMessage);
    }
    $conttp->setVariable('CLASS_NAME_1', 'elementHide');
    $conttp->setVariable('CLASS_NAME_2', 'elementHide');
    $conttp->setVariable('CLASS_NAME_3', 'elementHide');
    $conttp->setVariable('FILL_ALLFIELDS', translate('Please fill in all fields marked with an *'));
    $conttp->setVariable('FILL_ILLEGALCHARS', translate('The following field contains illegal characters:'));
    /* Insert data from database in "modify" mode */
    if (isset($arrModifyData) && ($chkSelModify === 'modify')) {
        /* Process data */
        $myContentClass->addInsertData($conttp, $arrModifyData, 0, '');
        /* Connection method */
        if ((int)$arrModifyData['method'] === 1) {
            $conttp->setVariable('FILE_SELECTED', 'selected');
        }
        if ((int)$arrModifyData['method'] === 2) {
            $conttp->setVariable('FTP_SELECTED', 'selected');
            $conttp->setVariable('CLASS_NAME_1', 'elementShow');
            $conttp->setVariable('CLASS_NAME_2', 'elementHide');
            $conttp->setVariable('CLASS_NAME_3', 'elementShow');
        }
        if ((int)$arrModifyData['method'] === 3) {
            $conttp->setVariable('SFTP_SELECTED', 'selected');
            $conttp->setVariable('CLASS_NAME_1', 'elementShow');
            $conttp->setVariable('CLASS_NAME_2', 'elementShow');
            $conttp->setVariable('CLASS_NAME_3', 'elementHide');
        }
        if ((int)$arrModifyData['ftp_secure'] === 1) {
            $conttp->setVariable('FTPS_CHECKED', 'checked');
        }
        /* Nagios version */
        $conttp->setVariable('VER_SELECTED_' . $arrModifyData['version'], 'selected');
        /* Domain localhost can't be renamed */
        if ($arrModifyData[$preKeyField] === 'localhost') {
            $conttp->setVariable('DOMAIN_DISABLE', 'readonly');
            $conttp->setVariable('LOCKCLASS', 'inputlock');
        } elseif ($arrModifyData[$preKeyField] === 'common') {
            $conttp->setVariable('DOMAIN_DISABLE', 'readonly');
            $conttp->setVariable('COMMON_INVISIBLE', 'class="elementHide"');
            $conttp->setVariable('LOCKCLASS', 'inputlock');
        }
    }
    if ($chkSelModify === 'errormodify') {
        $conttp->setVariable('DAT_TARGET', $chkTfValue1);
        /* Domain localhost can't be renamed */
        if ($chkTfValue1 === 'localhost') {
            $conttp->setVariable('DOMAIN_DISABLE', 'readonly');
            $conttp->setVariable('LOCKCLASS', 'inputlock');
        } elseif ($chkTfValue1 === 'common') {
            $conttp->setVariable('DOMAIN_DISABLE', 'readonly');
            $conttp->setVariable('COMMON_INVISIBLE', 'class="elementHide"');
            $conttp->setVariable('LOCKCLASS', 'inputlock');
        } else {
            $conttp->setVariable('LOCKCLASS', 'inpmust');
        }
        $conttp->setVariable('DAT_ALIAS', $chkTfValue2);
        $conttp->setVariable('DAT_SERVER', $chkTfValue4);
        /* Connection method */
        if ($chkSelValue1 === 1) {
            $conttp->setVariable('FILE_SELECTED', 'selected');
            $conttp->setVariable('CLASS_NAME_1', 'elementHide');
            $conttp->setVariable('CLASS_NAME_2', 'elementHide');
            $conttp->setVariable('CLASS_NAME_3', 'elementHide');
        }
        if ($chkSelValue1 === 2) {
            $conttp->setVariable('FTP_SELECTED', 'selected');
            $conttp->setVariable('CLASS_NAME_1', 'elementShow');
            $conttp->setVariable('CLASS_NAME_2', 'elementHide');
            $conttp->setVariable('CLASS_NAME_3', 'elementShow');
        }
        if ($chkSelValue1 === 3) {
            $conttp->setVariable('SFTP_SELECTED', 'selected');
            $conttp->setVariable('CLASS_NAME_1', 'elementShow');
            $conttp->setVariable('CLASS_NAME_2', 'elementShow');
            $conttp->setVariable('CLASS_NAME_3', 'elementHide');
        }
        $conttp->setVariable('DAT_USER', $chkTfValue5);
        $conttp->setVariable('DAT_SSH_KEY_PATH', $chkTfValue7);
        if ($chkChbValue1 === 1) {
            $conttp->setVariable('FTPS_CHECKED', 'checked');
        }
        $conttp->setVariable('DAT_BASEDIR', $chkTfValue8);
        $conttp->setVariable('DAT_HOSTCONFIG', $chkTfValue9);
        $conttp->setVariable('DAT_SERVICECONFIG', $chkTfValue10);
        $conttp->setVariable('DAT_BACKUPDIR', $chkTfValue11);
        $conttp->setVariable('DAT_HOSTBACKUP', $chkTfValue12);
        $conttp->setVariable('DAT_SERVICEBACKUP', $chkTfValue13);
        $conttp->setVariable('DAT_NAGIOSBASEDIR', $chkTfValue14);
        $conttp->setVariable('DAT_IMPORTDIR', $chkTfValue15);
        $conttp->setVariable('DAT_COMMANDFILE', $chkTfValue17);
        $conttp->setVariable('DAT_BINARYFILE', $chkTfValue18);
        $conttp->setVariable('DAT_PIDFILE', $chkTfValue19);
        $conttp->setVariable('DAT_CONFFILE', $chkTfValue20);
        $conttp->setVariable('DAT_CGIFILE', $chkTfValue21);
        $conttp->setVariable('DAT_RESOURCEFILE', $chkTfValue22);
        $conttp->setVariable('DAT_PICTUREDIR', $chkTfValue16);
        /* NagiosQL version */
        if ($chkSelValue2 === 1) {
            $conttp->setVariable('VER_SELECTED_1', 'selected');
        }
        if ($chkSelValue2 === 2) {
            $conttp->setVariable('VER_SELECTED_2', 'selected');
        }
        if ($chkSelValue2 === 3) {
            $conttp->setVariable('VER_SELECTED_3', 'selected');
        }
        /* Hidden variables */
        $conttp->setVariable('MODUS', filter_input(INPUT_POST, 'modus'));
        $conttp->setVariable('DAT_ID', filter_input(INPUT_POST, 'hidId', FILTER_VALIDATE_INT));
        $conttp->setVariable('LIMIT', filter_input(INPUT_POST, 'hidLimit', FILTER_VALIDATE_INT));
        /* Active */
        if (filter_input(INPUT_POST, 'chbActive')) {
            $conttp->setVariable('ACT_CHECKED', 'checked');
        } else {
            $conttp->setVariable('ACT_CHECKED');
        }
    }
    $conttp->parse('datainsert');
    $conttp->show('datainsert');
}
/*
List view
*/
if ($chkModus === 'display') {
    /* Initial list view definitions */
    $myContentClass->listViewInit($mastertp);
    $mastertp->setVariable('TITLE', translate('Configuration domain administration'));
    $mastertp->setVariable('FIELD_1', translate('Configuration target'));
    $mastertp->setVariable('FIELD_2', translate('Description'));
    /* Row sorting */
    $strOrderString = "ORDER BY `$preKeyField` $hidSortDir";
    if ($hidSortBy === 2) {
        $strOrderString = "ORDER BY `alias` $hidSortDir";
    }
    /* Count datasets */
    $intLineCount = 0;
    /** @noinspection SqlResolve */
    $strSQL = "SELECT count(*) AS `number` FROM `$preTableName` WHERE `access_group` IN ($strAccess)";
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
    $strSQL = "SELECT `id`, `$preKeyField`, `alias`, `active`, `nodelete`, `access_group` "
        . "FROM `$preTableName` WHERE `access_group` IN ($strAccess) $strOrderString "
        . "LIMIT $chkLimit," . $SETS['common']['pagelines'];
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