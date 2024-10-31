<?php
/* ----------------------------------------------------------------------------
 NagiosQL
-------------------------------------------------------------------------------
 (c) 2005-2023 by Martin Willisegger

 Project   : NagiosQL
 Component : Contact definitions
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
 * @var string $chkModus from prepend_adm.php -> Form work mode
 * @var int $chkDataId from prepend_adm.php -> Actual dataset id
 * @var string $chkSelModify from prepend_adm.php -> Modification selection value
 * @var int $hidSortBy from prepend_adm.php -> Sort data by
 * @var string $hidSortDir from prepend_adm.php -> Sort data direction (ASC, DESC)
 * @var int $chkLimit from prepend_adm.php / settings -> Data set count per page
 * @var int $intVersion from prepend_adm.php -> Nagios version
 * @var array $SETS Settings array
 * @var int $intGlobalWriteAccess from prepend_content.php -> Global admin write access
 * @var int $intWriteAccessId from prepend_content.php -> Admin write access to actual dataset id
 * @var string $strAccess from prepend_content.php -> List of read access group id's for actual user
 * @var string $preSQLCommon1 from prepend_content.php -> Common SQL part 1
 * @var string $strDomainWhere from prepend_adm.php -> Domain selection SQL part with table name
 * @var string $strDomainWhere2 from prepend_adm.php -> Domain selection SQL part without table name
 * @var string $chkTfValue1 from prepend_content.php -> Contact name
 * @var string $chkTfValue2 from prepend_content.php -> Contact description
 * @var string $chkTfValue3 from prepend_content.php -> Email address
 * @var string $chkTfValue4 from prepend_content.php -> Pager number
 * @var string $chkTfValue5 from prepend_content.php -> Additional address 1
 * @var string $chkTfValue6 from prepend_content.php -> Additional address 2
 * @var string $chkTfValue7 from prepend_content.php -> Additional address 3
 * @var string $chkTfValue8 from prepend_content.php -> Additional address 4
 * @var string $chkTfValue9 from prepend_content.php -> Additional address 5
 * @var string $chkTfValue10 from prepend_content.php -> Additional address 6
 * @var string $chkTfValue11 from prepend_content.php -> Name value if used as contact template
 * @var int $chkSelValue1 from prepend_content.php -> Time period hosts
 * @var int $chkSelValue2 from prepend_content.php -> Time period services
 * @var array $chkMselValue1 from prepend_content.php -> Contact groups
 * @var array $chkMselValue2 from prepend_content.php -> Host command
 * @var array $chkMselValue3 from prepend_content.php -> Service command
 * @var int $intMselValue1 from prepend_content.php -> Contact groups multiselect status value
 * @var int $intMselValue2 from prepend_content.php -> Host command multiselect status value
 * @var int $intMselValue3 from prepend_content.php -> Service command multiselect status value
 * @var string $chkChbGr1a from prepend_content.php -> Host options (d)
 * @var string $chkChbGr1b from prepend_content.php -> Host options (u)
 * @var string $chkChbGr1c from prepend_content.php -> Host options (r)
 * @var string $chkChbGr1d from prepend_content.php -> Host options (f)
 * @var string $chkChbGr1e from prepend_content.php -> Host options (s)
 * @var string $chkChbGr1f from prepend_content.php -> Host options (n)
 * @var string $chkChbGr2a from prepend_content.php -> Service options (w)
 * @var string $chkChbGr2b from prepend_content.php -> Service options (u)
 * @var string $chkChbGr2c from prepend_content.php -> Service options (c)
 * @var string $chkChbGr2d from prepend_content.php -> Service options (r)
 * @var string $chkChbGr2e from prepend_content.php -> Service options (f)
 * @var string $chkChbGr2f from prepend_content.php -> Service options (s)
 * @var string $chkChbGr2g from prepend_content.php -> Service options (n)
 * @var int $chkRadValue1 from prepend_content.php -> Contact groups multiselect options
 * @var int $chkRadValue2 from prepend_content.php -> Host alarming
 * @var int $chkRadValue3 from prepend_content.php -> Service alarming
 * @var int $chkRadValue4 from prepend_content.php -> Host command multiselect options
 * @var int $chkRadValue5 from prepend_content.php -> Service command multiselect options
 * @var int $chkRadValue6 from prepend_content.php -> Retain status information
 * @var int $chkRadValue7 from prepend_content.php -> Retain non-status information
 * @var int $chkRadValue8 from prepend_content.php -> Can submit command
 * @var string $chkTfNullVal1 from prepend_content.php -> Minimum importance
 * @var int $intVariables from prepend_content.php -> Form uses variable definitions
 * @var int $intTemplates from prepend_content.php -> Form uses template definitions
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
$prePageId = 14;
$preContent = 'admin/contacts.htm.tpl';
$preListTpl = 'admin/datalist.htm.tpl';
$preSearchSession = 'contact';
$preTableName = 'tbl_contact';
$preKeyField = 'contact_name';
$preAccess = 1;
$preFieldvars = 1;
$strErrorMessage = '';
$strInfoMessage = '';
$strConsistMessage = '';
$strDBWarning = '';
$intRet1 = 0;
$intRet2 = 0;
$intNoTime = 0;
/*
 * Default values for form variables
*/
if (!isset($intMselValue1)) {
    $intMselValue1 = 0;
}
if (!isset($intMselValue2)) {
    $intMselValue2 = 0;
}
if (!isset($intMselValue3)) {
    $intMselValue3 = 0;
}
/*
Include preprocessing file
*/
require $preBasePath . 'functions/prepend_adm.php';
require $preBasePath . 'functions/prepend_content.php';
/*
Checkbox data processing
*/
if (($intVersion === 3) || ($intVersion === 4)) {
    $strHO = substr($chkChbGr1a . $chkChbGr1b . $chkChbGr1c . $chkChbGr1d . $chkChbGr1e . $chkChbGr1f, 0, -1);
    $strSO = substr($chkChbGr2a . $chkChbGr2b . $chkChbGr2c . $chkChbGr2d . $chkChbGr2e . $chkChbGr2f . $chkChbGr2g, 0, -1);
} else {
    $strHO = substr($chkChbGr1a . $chkChbGr1b . $chkChbGr1c . $chkChbGr1d . $chkChbGr1f, 0, -1);
    $strSO = substr($chkChbGr2a . $chkChbGr2b . $chkChbGr2c . $chkChbGr2d . $chkChbGr2e . $chkChbGr2g, 0, -1);
}
/*
Add or modify data
*/
if ((($chkModus === 'insert') || ($chkModus === 'modify')) && ($intGlobalWriteAccess === 0)) {
    $strSQLx = "`$preTableName` SET `$preKeyField`='$chkTfValue1', `alias`='$chkTfValue2', "
        . "`contactgroups`=$intMselValue1, `contactgroups_tploptions`=$chkRadValue1, "
        . "`minimum_importance`=$chkTfNullVal1, "
        . "`host_notifications_enabled`='$chkRadValue2', `service_notifications_enabled`='$chkRadValue3', "
        . "`host_notification_period`='$chkSelValue1', `service_notification_period`='$chkSelValue2', "
        . "`host_notification_options`='$strHO', `host_notification_commands_tploptions`=$chkRadValue4, "
        . "`service_notification_options`='$strSO', `host_notification_commands`=$intMselValue2, "
        . "`service_notification_commands`=$intMselValue3, "
        . "`service_notification_commands_tploptions`=$chkRadValue5, `can_submit_commands`='$chkRadValue8', "
        . "`retain_status_information`='$chkRadValue6', `retain_nonstatus_information`='$chkRadValue7', "
        . "`email`='$chkTfValue3', `pager`='$chkTfValue4', `address1`='$chkTfValue5', `address2`='$chkTfValue6', "
        . "`address3`='$chkTfValue7', `address4`='$chkTfValue8', `address5`='$chkTfValue9', "
        . "`address6`='$chkTfValue10', `name`='$chkTfValue11', `use_variables`='$intVariables', "
        . "`use_template`=$intTemplates, $preSQLCommon1";
    if ($chkModus === 'insert') {
        $strSQL = 'INSERT INTO ' . $strSQLx;
    } else {
        $strSQL = 'UPDATE ' . $strSQLx . ' WHERE `id`=' . $chkDataId;
    }
    if ($intWriteAccessId === 0) {
        if ($chkTfValue1 !== '') {
            $intReturn = $myDataClass->dataInsert($strSQL, $intInsertId);
            if ($chkModus === 'insert') {
                $chkDataId = $intInsertId;
            }
            if ($intReturn === 1) {
                $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
            } else {
                $myVisClass->processMessage($myDataClass->strInfoMessage, $strInfoMessage);
                $myDataClass->updateStatusTable($preTableName);
                if ($chkModus === 'insert') {
                    $myDataClass->writeLog(translate('New contact inserted:') . ' ' . $chkTfValue1);
                }
                if ($chkModus === 'modify') {
                    $myDataClass->writeLog(translate('Contact modified:') . ' ' . $chkTfValue1);
                }
                /*
                Insert/update relations
                */
                if ($chkModus === 'insert') {
                    if ($intMselValue1 !== 0) {
                        $intRet1 = $myDataClass->dataInsertRelation(
                            'tbl_lnkContactToContactgroup',
                            $chkDataId,
                            $chkMselValue1
                        );
                    }
                    if (isset($intRet1) && ($intRet1 !== 0)) {
                        $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                    }
                    if ($intMselValue2 !== 0) {
                        $intRet2 = $myDataClass->dataInsertRelation(
                            'tbl_lnkContactToCommandHost',
                            $chkDataId,
                            $chkMselValue2
                        );
                    }
                    if (isset($intRet2) && ($intRet2 !== 0)) {
                        $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                    }
                    if ($intMselValue3 !== 0) {
                        $intRet3 = $myDataClass->dataInsertRelation(
                            'tbl_lnkContactToCommandService',
                            $chkDataId,
                            $chkMselValue3
                        );
                    }
                    if (isset($intRet3) && ($intRet3 !== 0)) {
                        $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                    }
                } elseif ($chkModus === 'modify') {
                    if ($intMselValue1 !== 0) {
                        $intRet1 = $myDataClass->dataUpdateRelation(
                            'tbl_lnkContactToContactgroup',
                            $chkDataId,
                            $chkMselValue1
                        );
                    } else {
                        $intRet1 = $myDataClass->dataDeleteRelation('tbl_lnkContactToContactgroup', $chkDataId);
                    }
                    if ($intRet1 !== 0) {
                        $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                    }
                    if ($intMselValue2 !== 0) {
                        $intRet2 = $myDataClass->dataUpdateRelation(
                            'tbl_lnkContactToCommandHost',
                            $chkDataId,
                            $chkMselValue2
                        );
                    } else {
                        $intRet2 = $myDataClass->dataDeleteRelation('tbl_lnkContactToCommandHost', $chkDataId);
                    }
                    if ($intRet2 !== 0) {
                        $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                    }
                    if ($intMselValue3 !== 0) {
                        $intRet3 = $myDataClass->dataUpdateRelation(
                            'tbl_lnkContactToCommandService',
                            $chkDataId,
                            $chkMselValue3
                        );
                    } else {
                        $intRet3 = $myDataClass->dataDeleteRelation('tbl_lnkContactToCommandService', $chkDataId);
                    }
                    if ($intRet3 !== 0) {
                        $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                    }
                }
                /*
                Insert/update templates from session data
                */
                if ($chkModus === 'modify') {
                    $strSQL = "DELETE FROM `tbl_lnkContactToContacttemplate` WHERE `idMaster`=$chkDataId";
                    $intReturn = $myDataClass->dataInsert($strSQL, $intInsertId);
                    if ($intReturn !== 0) {
                        $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                    }
                }
                if (isset($_SESSION['templatedefinition']) && is_array($_SESSION['templatedefinition']) &&
                    (count($_SESSION['templatedefinition']) !== 0)) {
                    $intSortId = 1;
                    foreach ($_SESSION['templatedefinition'] as $elem) {
                        if ((int)$elem['status'] === 0) {
                            $strSQL = 'INSERT INTO `tbl_lnkContactToContacttemplate` (`idMaster`,`idSlave`,'
                                . "`idTable`,`idSort`) VALUES ($chkDataId," . $elem['idSlave'] . ', '
                                . $elem['idTable'] . ',' . $intSortId . ')';
                            $intReturn = $myDataClass->dataInsert($strSQL, $intInsertId);
                            if ($intReturn !== 0) {
                                $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                            }
                        }
                        $intSortId++;
                    }
                }
                /*
                Insert/update variables from session data
                */
                if ($chkModus === 'modify') {
                    $strSQL1 = "SELECT * FROM `tbl_lnkContactToVariabledefinition` WHERE `idMaster`=$chkDataId";
                    $booReturn = $myDBClass->hasDataArray($strSQL1, $arrData, $intDataCount);
                    if ($booReturn === false) {
                        $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                    }
                    if ($intDataCount !== 0) {
                        foreach ($arrData as $elem) {
                            $strSQL = 'DELETE FROM `tbl_variabledefinition` WHERE `id`=' . $elem['idSlave'];
                            $intReturn = $myDataClass->dataInsert($strSQL, $intInsertId);
                            if ($intReturn !== 0) {
                                $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                            }
                        }
                    }
                    $strSQL = "DELETE FROM `tbl_lnkContactToVariabledefinition` WHERE `idMaster`=$chkDataId";
                    $intReturn = $myDataClass->dataInsert($strSQL, $intInsertId);
                    if ($intReturn !== 0) {
                        $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                    }
                }
                if (isset($_SESSION['variabledefinition']) && is_array($_SESSION['variabledefinition']) &&
                    (count($_SESSION['variabledefinition']) !== 0)) {
                    foreach ($_SESSION['variabledefinition'] as $elem) {
                        if ((int)$elem['status'] === 0) {
                            $strSQL = 'INSERT INTO `tbl_variabledefinition` (`name`,`value`,`last_modified`) '
                                . "VALUES ('" . $elem['definition'] . "','" . $elem['range'] . "',now())";
                            $intReturn = $myDataClass->dataInsert($strSQL, $intInsertId);
                            if ($intReturn !== 0) {
                                $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                            }
                            $strSQL = 'INSERT INTO `tbl_lnkContactToVariabledefinition` (`idMaster`,`idSlave`) '
                                . "VALUES ($chkDataId,$intInsertId)";
                            $intReturn = $myDataClass->dataInsert($strSQL, $intInsertId);
                            if ($intReturn !== 0) {
                                $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                            }
                        }
                    }
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
Get date/time of last database and config file manipulation
*/
$intReturn = $myConfigClass->lastModifiedFile($preTableName, $arrTimeData, $strTimeInfoString);
if ($intReturn !== 0) {
    $myVisClass->processMessage($myConfigClass->strErrorMessage, $strErrorMessage);
}
/*
Singe data form
*/
if ($chkModus === 'add') {
    $intDataWarning = 0;
    $conttp->setVariable('TITLE', translate('Define contacts (contacts.cfg)'));
    /* Do not show modified time list */
    $intNoTime = 1;
    /* Process access group selection field */
    $strWhere = '';
    if (isset($arrModifyData) && ($chkSelModify === 'modify')) {
        $strWhere = 'AND `id` <> ' . $arrModifyData['id'];
    }
    $strSQL5 = 'SELECT `id`,`template_name`, `active`, `config_id` '
        . "FROM `tbl_contacttemplate` WHERE $strDomainWhere2 ORDER BY `template_name`";
    $booReturn5 = $myDBClass->hasDataArray($strSQL5, $arrDataTpl, $intDataCountTpl);
    if ($booReturn5 === false) {
        $myVisClass->processMessage($myDBClass->strErrorMessage, $strErrorMessage);
    }
    if ($intDataCountTpl !== 0) {
        /** @var array $arrDataTpl */
        foreach ($arrDataTpl as $elem) {
            if ((int)$elem['active'] === 0) {
                $strActive = ' [inactive]';
                $conttp->setVariable('SPECIAL_STYLE', 'inactive_option');
            } else {
                $strActive = '';
                $conttp->setVariable('SPECIAL_STYLE');
            }
            if ((int)$elem['config_id'] === 0) {
                $strCommon = ' [common]';
            } else {
                $strCommon = '';
            }
            $conttp->setVariable('DAT_TEMPLATE', htmlspecialchars($elem['template_name'], ENT_QUOTES, 'UTF-8') .
                $strActive . $strCommon);
            $conttp->setVariable('DAT_TEMPLATE_ID', $elem['id'] . '::1');
        }
        $conttp->parse('template');
    }
    $strSQL6 = 'SELECT `id`, `name`, `active` '
        . "FROM `$preTableName` WHERE `name` <> '' $strWhere AND $strDomainWhere ORDER BY `name`";
    $booReturn6 = $myDBClass->hasDataArray($strSQL6, $arrDataHpl, $intDataCount);
    if ($booReturn6 === false) {
        $myVisClass->processMessage($myDBClass->strErrorMessage, $strErrorMessage);
    }
    if ($intDataCount !== 0) {
        /** @var array $arrDataHpl */
        foreach ($arrDataHpl as $elem) {
            if ((int)$elem['active'] === 0) {
                $strActive = ' [inactive]';
                $conttp->setVariable('SPECIAL_STYLE', 'inactive_option');
            } else {
                $strActive = '';
                $conttp->setVariable('SPECIAL_STYLE');
            }
            $conttp->setVariable('DAT_TEMPLATE', htmlspecialchars($elem['name'], ENT_QUOTES, 'UTF-8') . $strActive);
            $conttp->setVariable('DAT_TEMPLATE_ID', $elem['id'] . '::2');
        }
        $conttp->parse('template');
    }
    /* Process timeperiod selection fields */
    $intFieldId = $arrModifyData['host_notification_period'] ?? 0;
    $intReturn1 = $myVisClass->parseSelectSimple('tbl_timeperiod', 'timeperiod_name', 'host_time', 1, $intFieldId);
    if ($intReturn1 !== 0) {
        $myVisClass->processMessage($myVisClass->strErrorMessage, $strErrorMessage);
    }
    $intFieldId = $arrModifyData['service_notification_period'] ?? 0;
    $intReturn2 = $myVisClass->parseSelectSimple('tbl_timeperiod', 'timeperiod_name', 'service_time', 1, $intFieldId);
    if ($intReturn2 !== 0) {
        $myVisClass->processMessage($myVisClass->strErrorMessage, $strErrorMessage);
        $myVisClass->processMessage(translate('Attention, no time periods defined!'), $strDBWarning);
        $intDataWarning = 1;
    }
    /* Process command selection fields */
    $intFieldId = $arrModifyData['host_notification_commands'] ?? 0;
    $intReturn3 = $myVisClass->parseSelectMulti(
        'tbl_command',
        'command_name',
        'host_command',
        'tbl_lnkContactToCommandHost',
        0,
        $intFieldId
    );
    if ($intReturn3 !== 0) {
        $myVisClass->processMessage($myVisClass->strErrorMessage, $strErrorMessage);
    }
    $intFieldId = $arrModifyData['service_notification_commands'] ?? 0;
    $intReturn4 = $myVisClass->parseSelectMulti(
        'tbl_command',
        'command_name',
        'service_command',
        'tbl_lnkContactToCommandService',
        0,
        $intFieldId
    );
    if ($intReturn4 !== 0) {
        $myVisClass->processMessage($myVisClass->strErrorMessage, $strErrorMessage);
        $myVisClass->processMessage(translate('Attention, no commands defined!'), $strDBWarning);
        $intDataWarning = 1;
    }
    /* Process contactgroup selection field */
    $intFieldId = $arrModifyData['contactgroups'] ?? 0;
    $intReturn5 = $myVisClass->parseSelectMulti(
        'tbl_contactgroup',
        'contactgroup_name',
        'contactgroup',
        'tbl_lnkContactToContactgroup',
        2,
        $intFieldId
    );
    if ($intReturn5 !== 0) {
        $myVisClass->processMessage($myVisClass->strErrorMessage, $strErrorMessage);
    }
    /* Process access group selection field */
    $intFieldId = $arrModifyData['access_group'] ?? 0;
    $intReturn6 = $myVisClass->parseSelectSimple('tbl_group', 'groupname', 'acc_group', 0, $intFieldId);
    if ($intReturn6 !== 0) {
        $myVisClass->processMessage($myVisClass->strErrorMessage, $strErrorMessage);
    }
    /* Initial add/modify form definitions */
    $strChbFields = 'HNE,SNE,RSI,CSC,RNS,TPL,SEC,HOC,COG';
    $myContentClass->addFormInit($conttp, $strChbFields);
    if ($intDataWarning === 1) {
        $conttp->setVariable('WARNING', $strDBWarning . '<br>' . translate('Saving not possible!'));
        $conttp->setVariable('DISABLE_SAVE', 'disabled');
    }
    if ($intVersion === 4) {
        $conttp->setVariable('HOST_OPTION_FIELDS', 'chbGr1a,chbGr1b,chbGr1c,chbGr1d,chbGr1e,chbGr1f');
        $conttp->setVariable('SERVICE_OPTION_FIELDS', 'chbGr2a,chbGr2b,chbGr2c,chbGr2d,chbGr2e,chbGr2f,chbGr2g');
    }
    if ($intVersion === 3) {
        $conttp->setVariable('HOST_OPTION_FIELDS', 'chbGr1a,chbGr1b,chbGr1c,chbGr1d,chbGr1e,chbGr1f');
        $conttp->setVariable('SERVICE_OPTION_FIELDS', 'chbGr2a,chbGr2b,chbGr2c,chbGr2d,chbGr2e,chbGr2f,chbGr2g');
    }
    if ($intVersion < 3) {
        $conttp->setVariable('HOST_OPTION_FIELDS', 'chbGr1a,chbGr1b,chbGr1c,chbGr1d,chbGr1f');
        $conttp->setVariable('SERVICE_OPTION_FIELDS', 'chbGr2a,chbGr2b,chbGr2c,chbGr2d,chbGr2e,chbGr2g');
        $conttp->setVariable('VERSION_20_VALUE_MUST', ',tfValue2');
    }
    /* Insert data from database in "modify" mode */
    if (isset($arrModifyData) && ($chkSelModify === 'modify')) {
        /* Check relation information to find out locked configuration datasets */
        $intLocked = $myDataClass->infoRelation($preTableName, $arrModifyData['id'], $preKeyField);
        $myVisClass->processMessage($myDataClass->strInfoMessage, $strRelMessage);
        $strInfo = '<br><span class="redmessage">' . translate('Entry cannot be activated because it is used by '
                . 'another configuration') . ':</span>';
        $strInfo .= '<br><span class="greenmessage">' . $strRelMessage . '</span>';
        /* Process data */
        $myContentClass->addInsertData($conttp, $arrModifyData, $intLocked, $strInfo, $strChbFields);
        /* Process radio fields */
        $conttp->setVariable('DAT_HNE' . $arrModifyData['host_notifications_enabled'] . '_CHECKED', 'checked');
        $conttp->setVariable('DAT_SNE' . $arrModifyData['service_notifications_enabled'] . '_CHECKED', 'checked');
        $conttp->setVariable('DAT_RSI' . $arrModifyData['retain_status_information'] . '_CHECKED', 'checked');
        $conttp->setVariable('DAT_CSC' . $arrModifyData['can_submit_commands'] . '_CHECKED', 'checked');
        $conttp->setVariable('DAT_RNS' . $arrModifyData['retain_nonstatus_information'] . '_CHECKED', 'checked');
        $conttp->setVariable('DAT_TPL' . $arrModifyData['use_template_tploptions'] . '_CHECKED', 'checked');
        $conttp->setVariable(
            'DAT_SEC' . $arrModifyData['service_notification_commands_tploptions'] . '_CHECKED',
            'checked'
        );
        $conttp->setVariable('DAT_HOC' . $arrModifyData['host_notification_commands_tploptions'] . '_CHECKED', 'checked');
        $conttp->setVariable('DAT_COG' . $arrModifyData['contactgroups_tploptions'] . '_CHECKED', 'checked');
        /* Process option fields */
        foreach (explode(',', $arrModifyData['host_notification_options']) as $elem) {
            $conttp->setVariable('DAT_HO' . strtoupper($elem) . '_CHECKED', 'checked');
        }
        foreach (explode(',', $arrModifyData['service_notification_options']) as $elem) {
            $conttp->setVariable('DAT_SO' . strtoupper($elem) . '_CHECKED', 'checked');
        }
    }
    $conttp->parse('datainsert');
    $conttp->show('datainsert');
}
/*
List view
*/
if ($chkModus === 'display') {
    $strSearchWhere = '';
    $intLineCount = 0;
    /* Initial list view definitions */
    $myContentClass->listViewInit($mastertp);
    $mastertp->setVariable('TITLE', translate('Define contacts (contacts.cfg)'));
    $mastertp->setVariable('FIELD_1', translate('Contact name'));
    $mastertp->setVariable('FIELD_2', translate('Description'));
    $mastertp->setVariable('FILTER_VISIBLE', 'visibility: hidden');
    /* Process search string */
    if ($_SESSION['search'][$preSearchSession] !== '') {
        $strSearchTxt = $_SESSION['search'][$preSearchSession];
        $strSearchWhere = "AND (`$preKeyField` LIKE '%" . $strSearchTxt . "%' OR `alias` LIKE '%" . $strSearchTxt . "%' OR "
            . "`email` LIKE '%" . $strSearchTxt . "%' OR `pager` LIKE '%" . $strSearchTxt . "%' OR "
            . "`address1` LIKE '%" . $strSearchTxt . "%' OR `address2` LIKE '%" . $strSearchTxt . "%' OR "
            . "`address3` LIKE '%" . $strSearchTxt . "%' OR `address4` LIKE '%" . $strSearchTxt . "%' OR "
            . "`address5` LIKE '%" . $strSearchTxt . "%' OR `address6` LIKE '%" . $strSearchTxt . "%' OR "
            . "`name` LIKE '%" . $strSearchTxt . "%')";
    }
    /* Row sorting */
    $strOrderString = "ORDER BY `config_id`, `$preKeyField` $hidSortDir";
    if ($hidSortBy === 2) {
        $strOrderString = "ORDER BY `config_id`, `alias` $hidSortDir";
    }
    /* Count datasets */
    $strSQL7 = 'SELECT count(*) AS `number` '
        . "FROM `$preTableName` WHERE $strDomainWhere $strSearchWhere AND `access_group` IN ($strAccess)";
    $booReturn7 = $myDBClass->hasSingleDataset($strSQL7, $arrDataLinesCount);
    if ($booReturn7 === false) {
        $myVisClass->processMessage(translate('Error while selecting data from database:'), $strErrorMessage);
        $myVisClass->processMessage($myDBClass->strErrorMessage, $strErrorMessage);
    } else {
        $intLineCount = (int)$arrDataLinesCount['number'];
        if ($intLineCount < $chkLimit) {
            $chkLimit = 0;
        }
    }
    /* Get datasets */
    $strSQL8 = "SELECT `id`, `$preKeyField`, `alias`, `active`, `register`, `config_id`, `access_group` "
        . "FROM `$preTableName` WHERE $strDomainWhere $strSearchWhere AND `access_group` "
        . "IN ($strAccess) $strOrderString LIMIT $chkLimit," . $SETS['common']['pagelines'];
    $booReturn8 = $myDBClass->hasDataArray($strSQL8, $arrDataLines, $intDataCount);
    if ($booReturn8 === false) {
        $myVisClass->processMessage(translate('Error while selecting data from database:'), $strErrorMessage);
        $myVisClass->processMessage($myDBClass->strErrorMessage, $strErrorMessage);
    }
    /* Process data */
    $myContentClass->listData($mastertp, $arrDataLines, $intDataCount, $intLineCount, $preKeyField, 'alias');
}
/* Show messages */
$myContentClass->showMessages(
    $mastertp,
    $strErrorMessage,
    $strInfoMessage,
    $strConsistMessage,
    $arrTimeData,
    $strTimeInfoString,
    $intNoTime
);
/*
Process footer
 */
$myContentClass->showFooter($maintp, $setFileVersion);