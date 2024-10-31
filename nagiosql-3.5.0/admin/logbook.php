<?php
/* ----------------------------------------------------------------------------
 NagiosQL
-------------------------------------------------------------------------------
 (c) 2005-2023 by Martin Willisegger

 Project   : NagiosQL
 Component : Logbook administration
 Website   : https://sourceforge.net/projects/nagiosql/
 Version   : 3.5.0
 GIT Repo  : https://gitlab.com/wizonet/NagiosQL
-----------------------------------------------------------------------------*/

use functions\MysqliDbClass;
use functions\NagVisualClass;

/**
 * Class and variable includes
 * @var HTML_Template_IT $conttp Content template
 * @var HTML_Template_IT $maintp Main templat
 * @var MysqliDbClass $myDBClass MySQL database class
 * @var NagVisualClass $myVisClass Visual content class
 * @var string $setFileVersion from prepend_adm.php -> Application version string
 * @var string $prePageKey from prepend_adm.php -> Menu group id
 * @var array $SETS Settings array
 * @var string $chkTfValue1 from prepend_content.php -> User name
 * @var string $chkTfValue2 from prepend_content.php -> User description
 * @var string $chkTfSearch from prepend_content.php -> Text search string
 * @var int $chkFromLine from prepend_content.php -> Line number
 * @var array $arrDescription from fieldvars.php -> Translated common strings
 *
 *
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
$prePageId = 37;
$preContent = 'admin/logbook.htm.tpl';
$preAccess = 1;
$preFieldvars = 1;
$strErrorMessage = '';
$strInfoMessage = '';
/*
Include preprocessing file
*/
require $preBasePath . 'functions/prepend_adm.php';
require $preBasePath . 'functions/prepend_content.php';
/*
Delete log entries
*/
$strWhere = '';
if ($chkTfValue1 !== '') {
    $strWhere .= "AND `time` > '$chkTfValue1 00:00:00'";
}
if ($chkTfValue2 !== '') {
    $strWhere .= "AND `time` < '$chkTfValue2 23:59:59'";
}
if ($strWhere !== '') {
    $strSQL = 'DELETE FROM `tbl_logbook` WHERE `id`<>0 ';
    $strSQL .= $strWhere;
    $booReturn = $myDBClass->insertData($strSQL);
    if ($booReturn === false) {
        $myVisClass->processMessage(translate('Error while selecting data from database:'), $strErrorMessage);
        $myVisClass->processMessage($myDBClass->strErrorMessage, $strErrorMessage);
    } else {
        $myVisClass->processMessage(translate('Dataset successfully deleted. Affected rows:') . ' ' .
            $myDBClass->intAffectedRows, $strInfoMessage);
    }
}
/*
Search data
*/
if ($chkTfSearch !== '') {
    $strWhere = "WHERE `user` LIKE '%$chkTfSearch%' OR `ipadress` LIKE '%$chkTfSearch%' "
        . "OR `domain` LIKE '%$chkTfSearch%' OR `entry` LIKE '%$chkTfSearch%'";
} else {
    $strWhere = '';
}
/*
Get data
*/
$intNumRows = $myDBClass->getFieldData("SELECT count(*) FROM `tbl_logbook` $strWhere");
if ($intNumRows <= $chkFromLine) {
    $chkFromLine = 0;
}
$strSQL = "SELECT DATE_FORMAT(time,'%Y-%m-%d %H:%i:%s') AS `time`, `user`, `ipadress`, `domain`, `entry` "
    . "FROM `tbl_logbook` $strWhere ORDER BY `time` DESC LIMIT $chkFromLine," . $SETS['common']['pagelines'];
$booReturn = $myDBClass->hasDataArray($strSQL, $arrDataLines, $intDataCount);
if ($booReturn === false) {
    $myVisClass->processMessage(translate('Error while selecting data from database:'), $strErrorMessage);
    $myVisClass->processMessage($myDBClass->strErrorMessage, $strErrorMessage);
}
/*
Start content
*/
$conttp->setVariable('TITLE', translate('View logbook'));
foreach ($arrDescription as $elem) {
    $conttp->setVariable($elem['name'], $elem['string']);
}
$conttp->setVariable('LANG_ENTRIES_BEFORE', translate('Delete logentries between:'));
$conttp->setVariable('LOCALE', $SETS['data']['locale']);
$conttp->setVariable('LANG_SELECT_DATE', translate('Please supply a start or a stop time at least'));
$conttp->setVariable('LANG_DELETELOG', translate('Do you really want to delete all log entries between the '
    . 'selected dates?'));
$conttp->setVariable('DAT_SEARCH', $chkTfSearch);
// Legende einblenden
if ($chkFromLine > 1) {
    $intPrevNumber = $chkFromLine - 20;
    $conttp->setVariable('LANG_PREVIOUS', '<a href="' . filter_input(INPUT_SERVER, 'PHP_SELF') .
        '?from_line=' . $intPrevNumber . '"><< ' . translate('previous 20 entries') . '</a>');
} else {
    $conttp->setVariable('LANG_PREVIOUS');
}
if ($chkFromLine < $intNumRows - 20) {
    $intNextNumber = $chkFromLine + 20;
    $conttp->setVariable('LANG_NEXT', '<a href="' . filter_input(INPUT_SERVER, 'PHP_SELF') .
        '?from_line=' . $intNextNumber . '">' . translate('next 20 entries') . ' >></a>');
} else {
    $conttp->setVariable('LANG_NEXT');
}
/*
Output log data
*/
if ($intDataCount !== 0) {
    for ($i = 0; $i < $intDataCount; $i++) {
        /* Set default values */
        if ($arrDataLines[$i]['ipadress'] === '') {
            $arrDataLines[$i]['ipadress'] = '&nbsp;';
        }
        /* Insert data values */
        $conttp->setVariable('DAT_TIME', $arrDataLines[$i]['time']);
        $conttp->setVariable('DAT_ACCOUNT', $arrDataLines[$i]['user']);
        $conttp->setVariable('DAT_ACTION', $arrDataLines[$i]['entry']);
        $conttp->setVariable('DAT_IPADRESS', $arrDataLines[$i]['ipadress']);
        $conttp->setVariable('DAT_DOMAIN', $arrDataLines[$i]['domain']);
        $conttp->parse('logdatacell');
    }
}
$conttp->setVariable('ERRORMESSAGE', '<br>' . $strErrorMessage);
$conttp->setVariable('INFOMESSAGE', '<br>' . $strInfoMessage);
/* Check access rights for adding new objects */
if ($myVisClass->checkAccountGroup($prePageKey, 'write') !== 0) {
    $conttp->setVariable('ADD_CONTROL', 'disabled="disabled"');
}
$conttp->parse('logbooksite');
$conttp->show('logbooksite');
/*
Process footer
*/
$maintp->setVariable('VERSION_INFO', "<a href='https://sourceforge.net/projects/nagiosql/' "
    . "target='_blank'>NagiosQL</a> $setFileVersion");
$maintp->parse('footer');
$maintp->show('footer');