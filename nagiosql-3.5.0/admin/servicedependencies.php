<?php
/* ----------------------------------------------------------------------------
 NagiosQL
-------------------------------------------------------------------------------
 (c) 2005-2023 by Martin Willisegger

 Project   : NagiosQL
 Component : Service dependencies definition
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
 * @var int $chkRegister from prepend_adm.php -> Register checkbox
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
 * @var string $strSearchWhere from prepend_content.php -> SQL WHERE addon for data search
 * @var string $strDomainWhere from prepend_adm.php -> Domain selection SQL part with table name
 * @var string $chkSelAccGr from prepend_content.php -> Access group selector
 * @var string $chkTfValue1 from prepend_content.php -> Configuration name
 * @var int $chkSelValue1 from prepend_content.php -> Dependency period
 * @var array $chkMselValue1 from prepend_content.php -> Hosts
 * @var array $chkMselValue2 from prepend_content.php -> Dependent hosts
 * @var array $chkMselValue3 from prepend_content.php -> Hostgroups
 * @var array $chkMselValue4 from prepend_content.php -> Dependent hostgroups
 * @var array $chkMselValue5 from prepend_content.php -> Services
 * @var array $chkMselValue6 from prepend_content.php -> Dependent services
 * @var array $chkMselValue7 from prepend_content.php -> Servicegroups
 * @var array $chkMselValue8 from prepend_content.php -> Dependent servicegroups
 * @var int $intMselValue1 from prepend_content.php -> Hosts multiselect status value
 * @var int $intMselValue2 from prepend_content.php -> Dependent hosts multiselect status value
 * @var int $intMselValue3 from prepend_content.php -> Hostgroups multiselect status value
 * @var int $intMselValue4 from prepend_content.php -> Dependent hostgroups multiselect status value
 * @var int $intMselValue5 from prepend_content.php -> Services multiselect status value
 * @var int $intMselValue6 from prepend_content.php -> Dependent services multiselect status value
 * @var int $intMselValue7 from prepend_content.php -> Dependent servicegroups multiselect status value
 * @var int $intMselValue8 from prepend_content.php -> Hostgroups multiselect status value
 * @var string $chkChbGr1a from prepend_content.php -> Execution failure criteria (o)
 * @var string $chkChbGr1b from prepend_content.php -> Execution failure criteria (w)
 * @var string $chkChbGr1c from prepend_content.php -> Execution failure criteria (u)
 * @var string $chkChbGr1d from prepend_content.php -> Execution failure criteria (c)
 * @var string $chkChbGr1e from prepend_content.php -> Execution failure criteria (p)
 * @var string $chkChbGr1f from prepend_content.php -> Execution failure criteria (n)
 * @var string $chkChbGr2a from prepend_content.php -> Notification failure criteria (o)
 * @var string $chkChbGr2b from prepend_content.php -> Notification failure criteria (w)
 * @var string $chkChbGr2c from prepend_content.php -> Notification failure criteria (u)
 * @var string $chkChbGr2d from prepend_content.php -> Notification failure criteria (c)
 * @var string $chkChbGr2e from prepend_content.php -> Notification failure criteria (p)
 * @var string $chkChbGr2f from prepend_content.php -> Notification failure criteria (n)
 * @var int $chkChbValue1 from prepend_content.php -> Inherit parents
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
$prePageId = 22;
$preContent = 'admin/servicedependencies.htm.tpl';
$preListTpl = 'admin/datalist.htm.tpl';
$preSearchSession = 'servicedependencies';
$preTableName = 'tbl_servicedependency';
$preKeyField = 'config_name';
$preAccess = 1;
$preFieldvars = 1;
$strErrorMessage = '';
$strInfoMessage = '';
$strConsistMessage = '';
$strDBWarning = '';
$intDataWarning = 0;
$intRet1 = 0;
$intRet2 = 0;
$intRet3 = 0;
$intRet4 = 0;
$intRet5 = 0;
$intRet6 = 0;
$intRet7 = 0;
$intRet8 = 0;
$intNoTime = 0;
/*
Default values for form variables
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
if (!isset($intMselValue4)) {
    $intMselValue4 = 0;
}
if (!isset($intMselValue5)) {
    $intMselValue5 = 0;
}
if (!isset($intMselValue6)) {
    $intMselValue6 = 0;
}
if (!isset($intMselValue7)) {
    $intMselValue7 = 0;
}
if (!isset($intMselValue8)) {
    $intMselValue8 = 0;
}
/*
Include preprocessing files
*/
require $preBasePath . 'functions/prepend_adm.php';
require $preBasePath . 'functions/prepend_content.php';
/*
Data processing
*/
$strEO = substr($chkChbGr1a . $chkChbGr1b . $chkChbGr1c . $chkChbGr1d . $chkChbGr1e . $chkChbGr1f, 0, -1);
$strNO = substr($chkChbGr2a . $chkChbGr2b . $chkChbGr2c . $chkChbGr2d . $chkChbGr2e . $chkChbGr2f, 0, -1);
/*
Add or modify data
*/
if ((($chkModus === 'insert') || ($chkModus === 'modify')) && ($intGlobalWriteAccess === 0)) {
    $strSQLx = "`$preTableName` SET `dependent_host_name`=$intMselValue2, `dependent_hostgroup_name`=$intMselValue4, "
        . "`dependent_service_description`=$intMselValue6, `dependent_servicegroup_name`=$intMselValue8, "
        . "`host_name`=$intMselValue1, `hostgroup_name`=$intMselValue3, `service_description`=$intMselValue5, "
        . "`servicegroup_name`=$intMselValue7, `$preKeyField`='$chkTfValue1', `inherits_parent`='$chkChbValue1', "
        . "`execution_failure_criteria`='$strEO', `notification_failure_criteria`='$strNO', "
        . "`dependency_period`=$chkSelValue1, $preSQLCommon1";
    if ($chkModus === 'insert') {
        $strSQL = 'INSERT INTO ' . $strSQLx;
    } else {
        $strSQL = 'UPDATE ' . $strSQLx . ' WHERE `id`=' . $chkDataId;
    }
    if ($intWriteAccessId === 0) {
        if (($chkTfValue1 !== '') && (($intMselValue5 !== 0) || ($intMselValue7 !== 0)) &&
            (($intMselValue6 !== 0) || ($intMselValue8 !== 0))) {
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
                    $myDataClass->writeLog(translate('New service dependency inserted:') . ' ' . $chkTfValue1);
                }
                if ($chkModus === 'modify') {
                    $myDataClass->writeLog(translate('Service dependency modified:') . ' ' . $chkTfValue1);
                }
                /*
                Insert/update relations
                */
                if ($chkModus === 'insert') {
                    if ($intMselValue1 !== 0) {
                        $intRet1 = $myDataClass->dataInsertRelation(
                            'tbl_lnkServicedependencyToHost_H',
                            $chkDataId,
                            $chkMselValue1
                        );
                    }
                    if (isset($intRet1) && ($intRet1 !== 0)) {
                        $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                    }
                    if ($intMselValue2 !== 0) {
                        $intRet2 = $myDataClass->dataInsertRelation(
                            'tbl_lnkServicedependencyToHost_DH',
                            $chkDataId,
                            $chkMselValue2
                        );
                    }
                    if (isset($intRet2) && ($intRet2 !== 0)) {
                        $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                    }
                    if ($intMselValue3 !== 0) {
                        $intRet3 = $myDataClass->dataInsertRelation(
                            'tbl_lnkServicedependencyToHostgroup_H',
                            $chkDataId,
                            $chkMselValue3
                        );
                    }
                    if (isset($intRet3) && ($intRet3 !== 0)) {
                        $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                    }
                    if ($intMselValue4 !== 0) {
                        $intRet4 = $myDataClass->dataInsertRelation(
                            'tbl_lnkServicedependencyToHostgroup_DH',
                            $chkDataId,
                            $chkMselValue4
                        );
                    }
                    if (isset($intRet4) && ($intRet4 !== 0)) {
                        $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                    }
                    if ($intMselValue5 !== 0) {
                        $intRet5 = $myDataClass->dataInsertRelation(
                            'tbl_lnkServicedependencyToService_S',
                            $chkDataId,
                            $chkMselValue5
                        );
                    }
                    if (isset($intRet5) && ($intRet5 !== 0)) {
                        $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                    }
                    if ($intMselValue6 !== 0) {
                        $intRet6 = $myDataClass->dataInsertRelation(
                            'tbl_lnkServicedependencyToService_DS',
                            $chkDataId,
                            $chkMselValue6
                        );
                    }
                    if (isset($intRet6) && ($intRet6 !== 0)) {
                        $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                    }
                    if ($intMselValue7 !== 0) {
                        $intRet7 = $myDataClass->dataInsertRelation(
                            'tbl_lnkServicedependencyToServicegroup_S',
                            $chkDataId,
                            $chkMselValue7
                        );
                    }
                    if (isset($intRet7) && ($intRet7 !== 0)) {
                        $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                    }
                    if ($intMselValue8 !== 0) {
                        $intRet8 = $myDataClass->dataInsertRelation(
                            'tbl_lnkServicedependencyToServicegroup_DS',
                            $chkDataId,
                            $chkMselValue8
                        );
                    }
                    if (isset($intRet8) && ($intRet8 !== 0)) {
                        $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                    }
                } elseif ($chkModus === 'modify') {
                    if ($intMselValue1 !== 0) {
                        $intRet1 = $myDataClass->dataUpdateRelation(
                            'tbl_lnkServicedependencyToHost_H',
                            $chkDataId,
                            $chkMselValue1
                        );
                    } else {
                        $intRet1 = $myDataClass->dataDeleteRelation('tbl_lnkServicedependencyToHost_H', $chkDataId);
                    }
                    if ($intRet1 !== 0) {
                        $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                    }
                    if ($intMselValue2 !== 0) {
                        $intRet2 = $myDataClass->dataUpdateRelation(
                            'tbl_lnkServicedependencyToHost_DH',
                            $chkDataId,
                            $chkMselValue2
                        );
                    } else {
                        $intRet2 = $myDataClass->dataDeleteRelation('tbl_lnkServicedependencyToHost_DH', $chkDataId);
                    }
                    if ($intRet2 !== 0) {
                        $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                    }
                    if ($intMselValue3 !== 0) {
                        $intRet3 = $myDataClass->dataUpdateRelation(
                            'tbl_lnkServicedependencyToHostgroup_H',
                            $chkDataId,
                            $chkMselValue3
                        );
                    } else {
                        $intRet3 = $myDataClass->dataDeleteRelation(
                            'tbl_lnkServicedependencyToHostgroup_H',
                            $chkDataId
                        );
                    }
                    if ($intRet3 !== 0) {
                        $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                    }
                    if ($intMselValue4 !== 0) {
                        $intRet4 = $myDataClass->dataUpdateRelation(
                            'tbl_lnkServicedependencyToHostgroup_DH',
                            $chkDataId,
                            $chkMselValue4
                        );
                    } else {
                        $intRet4 = $myDataClass->dataDeleteRelation(
                            'tbl_lnkServicedependencyToHostgroup_DH',
                            $chkDataId
                        );
                    }
                    if ($intRet4 !== 0) {
                        $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                    }
                    if ($intMselValue5 !== 0) {
                        $intRet5 = $myDataClass->dataUpdateRelation(
                            'tbl_lnkServicedependencyToService_S',
                            $chkDataId,
                            $chkMselValue5
                        );
                    } else {
                        $intRet5 = $myDataClass->dataDeleteRelation(
                            'tbl_lnkServicedependencyToService_S',
                            $chkDataId
                        );
                    }
                    if ($intRet5 !== 0) {
                        $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                    }
                    if ($intMselValue6 !== 0) {
                        $intRet6 = $myDataClass->dataUpdateRelation(
                            'tbl_lnkServicedependencyToService_DS',
                            $chkDataId,
                            $chkMselValue6
                        );
                    } else {
                        $intRet6 = $myDataClass->dataDeleteRelation(
                            'tbl_lnkServicedependencyToService_DS',
                            $chkDataId
                        );
                    }
                    if ($intRet6 !== 0) {
                        $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                    }
                    if ($intMselValue7 !== 0) {
                        $intRet7 = $myDataClass->dataUpdateRelation(
                            'tbl_lnkServicedependencyToServicegroup_S',
                            $chkDataId,
                            $chkMselValue7
                        );
                    } else {
                        $intRet7 = $myDataClass->dataDeleteRelation(
                            'tbl_lnkServicedependencyToServicegroup_S',
                            $chkDataId
                        );
                    }
                    if ($intRet7 !== 0) {
                        $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                    }
                    if ($intMselValue8 !== 0) {
                        $intRet8 = $myDataClass->dataUpdateRelation(
                            'tbl_lnkServicedependencyToServicegroup_DS',
                            $chkDataId,
                            $chkMselValue8
                        );
                    } else {
                        $intRet8 = $myDataClass->dataDeleteRelation(
                            'tbl_lnkServicedependencyToServicegroup_DS',
                            $chkDataId
                        );
                    }
                    if ($intRet8 !== 0) {
                        $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                    }
                }
                if (($intRet1 + $intRet2 + $intRet3 + $intRet4 + $intRet5 + $intRet6 + $intRet7 + $intRet8) !== 0) {
                    $strInfoMessage = '';
                }
                /*
                Update Import HASH
                */
                $booReturn = $myDataClass->updateHash($preTableName, $chkDataId);
                if ($booReturn !== 0) {
                    $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
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
if (($chkModus !== 'add') && ($chkModus !== 'refresh')) {
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
if (($chkModus === 'add') || ($chkModus === 'refresh')) {
    $conttp->setVariable('TITLE', translate('Define service dependencies (servicedependencies.cfg)'));
    $_SESSION['refresh']['sd_host'] = $chkMselValue1;
    $_SESSION['refresh']['sd_dependent_host'] = $chkMselValue2;
    $_SESSION['refresh']['sd_hostgroup'] = $chkMselValue3;
    $_SESSION['refresh']['sd_dependent_hostgroup'] = $chkMselValue4;
    $_SESSION['refresh']['sd_service'] = $chkMselValue5;
    $_SESSION['refresh']['sd_dependent_service'] = $chkMselValue6;
    $_SESSION['refresh']['sd_servicegroup'] = $chkMselValue7;
    $_SESSION['refresh']['sd_dependent_servicegroup'] = $chkMselValue8;
    if ($chkModus !== 'refresh') {
        if (isset($arrModifyData['dependent_host_name']) && ($arrModifyData['dependent_host_name'] > 0)) {
            $arrTemp = array();
            $strSQL = 'SELECT `idSlave`, `exclude`  FROM `tbl_lnkServicedependencyToHost_DH` '
                . 'WHERE `idMaster` = ' . $arrModifyData['id'];
            $booReturn = $myDBClass->hasDataArray($strSQL, $arrData, $intDC);
            if ($booReturn === false) {
                $myVisClass->processMessage($myDBClass->strErrorMessage, $strErrorMessage);
            }
            if ($booReturn && ($intDC !== 0)) {
                foreach ($arrData as $elem) {
                    if ($elem['exclude'] === 1) {
                        $arrTemp[] = 'e' . $elem['idSlave'];
                    } else {
                        $arrTemp[] = $elem['idSlave'];
                    }
                }
            }
            if ($arrModifyData['dependent_host_name'] === 2) {
                $arrTemp[] = '*';
            }
            $_SESSION['refresh']['sd_dependent_host'] = $arrTemp;
        }
        if (isset($arrModifyData['host_name']) && ($arrModifyData['host_name'] > 0)) {
            $arrTemp = array();
            $strSQL = 'SELECT `idSlave`, `exclude` FROM `tbl_lnkServicedependencyToHost_H` '
                . 'WHERE `idMaster` = ' . $arrModifyData['id'];
            $booReturn = $myDBClass->hasDataArray($strSQL, $arrData, $intDC);
            if ($booReturn === false) {
                $myVisClass->processMessage($myDBClass->strErrorMessage, $strErrorMessage);
            }
            if ($intDC !== 0) {
                foreach ($arrData as $elem) {
                    if ($elem['exclude'] === 1) {
                        $arrTemp[] = 'e' . $elem['idSlave'];
                    } else {
                        $arrTemp[] = $elem['idSlave'];
                    }
                }
            }
            if ($arrModifyData['host_name'] === 2) {
                $arrTemp[] = '*';
            }
            $_SESSION['refresh']['sd_host'] = $arrTemp;
        }
        if (isset($arrModifyData['dependent_hostgroup_name']) && ($arrModifyData['dependent_hostgroup_name'] > 0)) {
            $arrTemp = array();
            $strSQL = 'SELECT `idSlave`, `exclude`  FROM `tbl_lnkServicedependencyToHostgroup_DH` '
                . 'WHERE `idMaster` = ' . $arrModifyData['id'];
            $booReturn = $myDBClass->hasDataArray($strSQL, $arrData, $intDC);
            if ($booReturn === false) {
                $myVisClass->processMessage($myDBClass->strErrorMessage, $strErrorMessage);
            }
            if ($intDC !== 0) {
                foreach ($arrData as $elem) {
                    if ($elem['exclude'] === 1) {
                        $arrTemp[] = 'e' . $elem['idSlave'];
                    } else {
                        $arrTemp[] = $elem['idSlave'];
                    }
                }
            }
            if ($arrModifyData['dependent_hostgroup_name'] === 2) {
                $arrTemp[] = '*';
            }
            $_SESSION['refresh']['sd_dependent_hostgroup'] = $arrTemp;
        }
        if (isset($arrModifyData['hostgroup_name']) && ($arrModifyData['hostgroup_name'] > 0)) {
            $arrTemp = array();
            $strSQL = 'SELECT `idSlave`, `exclude`  FROM `tbl_lnkServicedependencyToHostgroup_H` '
                . 'WHERE `idMaster` = ' . $arrModifyData['id'];
            $booReturn = $myDBClass->hasDataArray($strSQL, $arrData, $intDC);
            if ($booReturn === false) {
                $myVisClass->processMessage($myDBClass->strErrorMessage, $strErrorMessage);
            }
            if ($intDC !== 0) {
                foreach ($arrData as $elem) {
                    if ($elem['exclude'] === 1) {
                        $arrTemp[] = 'e' . $elem['idSlave'];
                    } else {
                        $arrTemp[] = $elem['idSlave'];
                    }
                }
            }
            if ($arrModifyData['hostgroup_name'] === 2) {
                $arrTemp[] = '*';
            }
            $_SESSION['refresh']['sd_hostgroup'] = $arrTemp;
        }
    }
    $myVisClass->arrSession = $_SESSION;
    /* Do not show modified time list */
    $intNoTime = 1;
    /* Process host selection field */
    $intFieldId = $arrModifyData['dependent_host_name'] ?? 0;
    if (($chkModus === 'refresh') && is_array($chkMselValue2) && (count($chkMselValue2) !== 0)) {
        $strRefresh = 'sd_dependent_host';
    } else {
        $strRefresh = '';
    }
    $intReturn1 = $myVisClass->parseSelectMulti(
        'tbl_host',
        'host_name',
        'dependent_host',
        'tbl_lnkServicedependencyToHost_DH',
        0,
        $intFieldId,
        -9,
        $strRefresh
    );
    if ($intReturn1 !== 0) {
        $myVisClass->processMessage($myVisClass->strErrorMessage, $strErrorMessage);
    }
    $intFieldId = $arrModifyData['host_name'] ?? 0;
    if (($chkModus === 'refresh') && is_array($chkMselValue1) && (count($chkMselValue1) !== 0)) {
        $strRefresh = 'sd_host';
    } else {
        $strRefresh = '';
    }
    $intReturn1 = $myVisClass->parseSelectMulti(
        'tbl_host',
        'host_name',
        'host',
        'tbl_lnkServicedependencyToHost_H',
        0,
        $intFieldId,
        -9,
        $strRefresh
    );
    if ($intReturn1 !== 0) {
        $myVisClass->processMessage($myVisClass->strErrorMessage, $strErrorMessage);
    }
    /* Process time period selection field */
    $intFieldId = $arrModifyData['dependency_period'] ?? 0;
    if ($chkModus === 'refresh') {
        $intFieldId = $chkSelValue1;
    }
    $intReturn = $myVisClass->parseSelectSimple('tbl_timeperiod', 'timeperiod_name', 'timeperiod', 1, $intFieldId);
    if ($intReturn !== 0) {
        $myVisClass->processMessage($myVisClass->strErrorMessage, $strErrorMessage);
    }
    /* Process host group selection field */
    $intFieldId = $arrModifyData['dependent_hostgroup_name'] ?? 0;
    if (($chkModus === 'refresh') && is_array($chkMselValue4) && (count($chkMselValue4) !== 0)) {
        $strRefresh = 'sd_dependent_hostgroup';
    } else {
        $strRefresh = '';
    }
    $intReturn2 = $myVisClass->parseSelectMulti(
        'tbl_hostgroup',
        'hostgroup_name',
        'dependent_hostgroup',
        'tbl_lnkServicedependencyToHostgroup_DH',
        0,
        $intFieldId,
        -9,
        $strRefresh
    );
    if ($intReturn2 !== 0) {
        $myVisClass->processMessage($myVisClass->strErrorMessage, $strErrorMessage);
    }
    $intFieldId = $arrModifyData['hostgroup_name'] ?? 0;
    if (($chkModus === 'refresh') && is_array($chkMselValue3) && (count($chkMselValue3) !== 0)) {
        $strRefresh = 'sd_hostgroup';
    } else {
        $strRefresh = '';
    }
    $intReturn2 = $myVisClass->parseSelectMulti(
        'tbl_hostgroup',
        'hostgroup_name',
        'hostgroup',
        'tbl_lnkServicedependencyToHostgroup_H',
        0,
        $intFieldId,
        -9,
        $strRefresh
    );
    if ($intReturn2 !== 0) {
        $myVisClass->processMessage($myVisClass->strErrorMessage, $strErrorMessage);
    }
    if (($intReturn1 !== 0) && ($intReturn2 !== 0)) {
        $myVisClass->processMessage(translate('Attention, no hosts and hostgroups defined!'), $strDBWarning);
        $intDataWarning = 1;
    }
    /* Process services selection field */
    $intFieldId = $arrModifyData['dependent_service_description'] ?? 0;
    if (($chkModus === 'refresh') && is_array($chkMselValue6) && (count($chkMselValue6) !== 0)) {
        $strRefresh = 'sd_dependent_service';
    } else {
        $strRefresh = '';
    }
    $intReturn = $myVisClass->parseSelectMulti(
        'tbl_service',
        'service_description',
        'dependent_service',
        'tbl_lnkServicedependencyToService_DS',
        2,
        $intFieldId,
        -9,
        $strRefresh
    );
    if ($intReturn !== 0) {
        $myVisClass->processMessage($myVisClass->strErrorMessage, $strErrorMessage);
    }
    $intFieldId = $arrModifyData['service_description'] ?? 0;
    if (($chkModus === 'refresh') && is_array($chkMselValue5) && (count($chkMselValue5) !== 0)) {
        $strRefresh = 'sd_service';
    } else {
        $strRefresh = '';
    }
    $intReturn = $myVisClass->parseSelectMulti(
        'tbl_service',
        'service_description',
        'service',
        'tbl_lnkServicedependencyToService_S',
        2,
        $intFieldId,
        -9,
        $strRefresh
    );
    if ($intReturn !== 0) {
        $myVisClass->processMessage($myVisClass->strErrorMessage, $strErrorMessage);
    }
    /* Process servicegroup selection field */
    $intFieldId = $arrModifyData['dependent_servicegroup_name'] ?? 0;
    if (($chkModus === 'refresh') && is_array($chkMselValue8) && (count($chkMselValue8) !== 0)) {
        $strRefresh = 'sd_dependent_servicegroup';
    } else {
        $strRefresh = '';
    }
    $intReturn = $myVisClass->parseSelectMulti(
        'tbl_servicegroup',
        'servicegroup_name',
        'dependent_servicegroup',
        'tbl_lnkServicedependencyToServicegroup_DS',
        0,
        $intFieldId,
        -9,
        $strRefresh
    );
    if ($intReturn !== 0) {
        $myVisClass->processMessage($myVisClass->strErrorMessage, $strErrorMessage);
    }
    $intFieldId = $arrModifyData['servicegroup_name'] ?? 0;
    if (($chkModus === 'refresh') && is_array($chkMselValue7) && (count($chkMselValue7) !== 0)) {
        $strRefresh = 'sd_servicegroup';
    } else {
        $strRefresh = '';
    }
    $intReturn = $myVisClass->parseSelectMulti(
        'tbl_servicegroup',
        'servicegroup_name',
        'servicegroup',
        'tbl_lnkServicedependencyToServicegroup_S',
        0,
        $intFieldId,
        -9,
        $strRefresh
    );
    if ($intReturn !== 0) {
        $myVisClass->processMessage($myVisClass->strErrorMessage, $strErrorMessage);
    }
    /* Process access group selection field */
    $intFieldId = $arrModifyData['access_group'] ?? 0;
    if ($chkModus === 'refresh') {
        $intFieldId = $chkSelAccGr;
    }
    $intReturn = $myVisClass->parseSelectSimple('tbl_group', 'groupname', 'acc_group', 0, $intFieldId);
    if ($intReturn !== 0) {
        $myVisClass->processMessage($myVisClass->strErrorMessage, $strErrorMessage);
    }
    /* Initial add/modify form definitions */
    $myContentClass->addFormInit($conttp);
    if ($intDataWarning === 1) {
        $conttp->setVariable('WARNING', $strDBWarning . '<br>' . translate('Saving not possible!'));
    }
    if ($intVersion < 3) {
        $conttp->setVariable('VERSION_20_VALUE_MUST', 'mselValue1,');
    }
    if ($chkModus === 'refresh') {
        if ($chkTfValue1 !== '') {
            $conttp->setVariable('DAT_CONFIG_NAME', $chkTfValue1);
        }
        foreach (explode(',', $strEO) as $elem) {
            $conttp->setVariable('DAT_EO' . strtoupper($elem) . '_CHECKED', 'checked');
        }
        foreach (explode(',', $strNO) as $elem) {
            $conttp->setVariable('DAT_NO' . strtoupper($elem) . '_CHECKED', 'checked');
        }
        if ($chkActive !== 1) {
            $conttp->setVariable('ACT_CHECKED');
        }
        if ($chkRegister !== 1) {
            $conttp->setVariable('REG_CHECKED');
        }
        if ($chkChbValue1 === 1) {
            $conttp->setVariable('ACT_INHERIT', 'checked');
        }
        if ($chkDataId !== 0) {
            $conttp->setVariable('DAT_ID', $chkDataId);
            $conttp->setVariable('MODUS', 'modify');
        }
        /* Insert data from database in "modify" mode */
    } elseif (isset($arrModifyData) && ($chkSelModify === 'modify')) {
        /* Check relation information to find out locked configuration datasets */
        $intLocked = $myDataClass->infoRelation($preTableName, $arrModifyData['id'], $preKeyField);
        $myVisClass->processMessage($myDataClass->strInfoMessage, $strRelMessage);
        $strInfo = '<br><span class="redmessage">' . translate('Entry cannot be activated because it is used by '
                . 'another configuration') . ':</span>';
        $strInfo .= '<br><span class="greenmessage">' . $strRelMessage . '</span>';
        /* Process data */
        $myContentClass->addInsertData($conttp, $arrModifyData, $intLocked, $strInfo);
        /* Setting special data */
        if ((int)$arrModifyData['inherits_parent'] === 1) {
            $conttp->setVariable('ACT_INHERIT', 'checked');
        }
        foreach (explode(',', $arrModifyData['execution_failure_criteria']) as $elem) {
            $conttp->setVariable('DAT_EO' . strtoupper($elem) . '_CHECKED', 'checked');
        }
        foreach (explode(',', $arrModifyData['notification_failure_criteria']) as $elem) {
            $conttp->setVariable('DAT_NO' . strtoupper($elem) . '_CHECKED', 'checked');
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
    $mastertp->setVariable('TITLE', translate('Define service dependencies (servicedependencies.cfg)'));
    $mastertp->setVariable('FIELD_1', translate('Config name'));
    $mastertp->setVariable('FIELD_2', translate('Dependent services'));
    $mastertp->setVariable('FILTER_VISIBLE', 'visibility: hidden');
    /* Process search string */
    if ($_SESSION['search'][$preSearchSession] !== '') {
        $strSearchTxt = $_SESSION['search'][$preSearchSession];
        $strSearchWhere = "AND (`$preKeyField` LIKE '%" . $strSearchTxt . "%')";
    }
    /* Row sorting */
    $strOrderString = "ORDER BY `config_id`, `$preKeyField` $hidSortDir";
    if ($hidSortBy === 2) {
        $strOrderString = "ORDER BY `config_id`, `$preKeyField` $hidSortDir";
    }
    $mastertp->setVariable('DISABLE_SORT_2', 'disable');
    /* Count datasets */
    $intLineCount = 0;
    /** @noinspection SqlResolve */
    $strSQL = "SELECT count(*) AS `number` FROM `$preTableName` "
        . "WHERE $strDomainWhere $strSearchWhere AND `access_group` IN ($strAccess)";
    $booReturn = $myDBClass->hasSingleDataset($strSQL, $arrDataLinesCount);
    if ($booReturn === false) {
        $myVisClass->processMessage(translate('Error while selecting data from database:'), $strErrorMessage);
        $myVisClass->processMessage($myDBClass->strErrorMessage, $strErrorMessage);
    } else {
        $intLineCount = (int)$arrDataLinesCount['number'];
        if ($intLineCount < $chkLimit) {
            $chkLimit = 0;
        }
    }
    /* Get datasets */
    $strSQL = "SELECT `id`, `$preKeyField`, `dependent_service_description`, `register`, `active`, `config_id`, "
        . "`access_group` FROM `$preTableName`WHERE $strDomainWhere $strSearchWhere AND `access_group` "
        . "IN ($strAccess) $strOrderString LIMIT $chkLimit," . $SETS['common']['pagelines'];
    $booReturn = $myDBClass->hasDataArray($strSQL, $arrDataLines, $intDataCount);
    if ($booReturn === false) {
        $myVisClass->processMessage(translate('Error while selecting data from database:'), $strErrorMessage);
        $myVisClass->processMessage($myDBClass->strErrorMessage, $strErrorMessage);
    }
    /* Process data */
    $myContentClass->listData(
        $mastertp,
        $arrDataLines,
        $intDataCount,
        $intLineCount,
        $preKeyField,
        'process_field',
        40
    );
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