<?php
/* ----------------------------------------------------------------------------
 NagiosQL
-------------------------------------------------------------------------------
 (c) 2005-2023 by Martin Willisegger

 Project   : NagiosQL
 Component : Group user administration
 Website   : https://sourceforge.net/projects/nagiosql/
 Version   : 3.5.0
 GIT Repo  : https://gitlab.com/wizonet/NagiosQL
-----------------------------------------------------------------------------*/

use functions\MysqliDbClass;

/**
 * Class and variable includes
 * @var MysqliDbClass $myDBClass MySQL database class
 * @var int $chkDataId from prepend_adm.php -> Actual dataset id
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
$preAccess = 1;
$preNoMain = 1;
require $preBasePath . 'functions/prepend_adm.php';
/*
Process get parameters
*/
$chkDataId = filter_input(INPUT_GET, 'dataId', FILTER_VALIDATE_INT, array('options' => array('default' => 0)));
$chkVersion = filter_input(INPUT_GET, 'version', FILTER_VALIDATE_INT, array('options' => array('default' => 0)));
$chkMode = (string)filter_input(INPUT_GET, 'mode');
$chkUser = (string)filter_input(INPUT_GET, 'user');
$chkRights = (string)filter_input(INPUT_GET, 'rights');
$chkId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$chkLinkTab = (string)filter_input(INPUT_GET, 'linktab');
if (substr_count($chkRights, '-')) {
    $arrRights = explode('-', $chkRights);
    $strRights = '';
    if ((int)$arrRights[0] === 1) {
        $strRights .= 'READ,';
    }
    if ((int)$arrRights[1] === 1) {
        $strRights .= 'WRITE,';
    }
    if ((int)$arrRights[2] === 1) {
        $strRights .= 'LINK,';
    }
    if ($strRights !== '') {
        $strRights = substr($strRights, 0, -1);
    }
    $chkRights = $strRights;
}
/*
Get datasets
*/
if ($chkLinkTab !== '') {
    /** @noinspection SqlResolve */
    $strSQL = 'SELECT * FROM `tbl_user` LEFT JOIN `' . $chkLinkTab . '` ON `id`=`idSlave` '
        . "WHERE `idMaster`=$chkDataId ORDER BY `username`";
    $booReturn = $myDBClass->hasDataArray($strSQL, $arrDataLines, $intDataCount);
    /*
    Write data to session
    */
    if (($chkMode === null) || ($chkMode === '')) {
        $_SESSION['groupuser'] = array();
        if ($intDataCount !== 0) {
            foreach ($arrDataLines as $elem) {
                $arrTemp['id'] = (int)$elem['id'];
                $arrTemp['user'] = (int)$elem['id'];
                $strRights = '';
                if ((int)$elem['read'] === 1) {
                    $strRights .= 'READ,';
                }
                if ((int)$elem['write'] === 1) {
                    $strRights .= 'WRITE,';
                }
                if ((int)$elem['link'] === 1) {
                    $strRights .= 'LINK,';
                }
                if ($strRights !== '') {
                    $strRights = substr($strRights, 0, -1);
                }
                $arrTemp['rights'] = $strRights;
                $arrTemp['status'] = 0;
                $_SESSION['groupuser'][] = $arrTemp;
            }
        }
    }
}
/*
Add mode
*/
if ($chkMode === 'add') {
    if (isset($_SESSION['groupuser']) && is_array($_SESSION['groupuser'])) {
        $intCheck = 0;
        foreach ($_SESSION['groupuser'] as $key => $elem) {
            if (((int)$elem['user'] === (int)$chkUser) && ((int)$elem['status'] === 0)) {
                $_SESSION['groupuser'][$key]['user'] = $chkUser;
                $_SESSION['groupuser'][$key]['rights'] = $chkRights;
                $intCheck = 1;
            }
        }
        if ($intCheck === 0) {
            $arrTemp['id'] = 0;
            $arrTemp['user'] = $chkUser;
            $arrTemp['rights'] = $chkRights;
            $arrTemp['status'] = 0;
            $_SESSION['groupuser'][] = $arrTemp;
        }
    } else {
        $arrTemp['id'] = 0;
        $arrTemp['user'] = $chkUser;
        $arrTemp['rights'] = $chkRights;
        $arrTemp['status'] = 0;
        $_SESSION['groupuser'] = array();
        $_SESSION['groupuser'][] = $arrTemp;
    }
}
/*
Deletion mode
*/
if ($chkMode === 'del' && isset($_SESSION['groupuser']) && is_array($_SESSION['groupuser'])) {
    foreach ($_SESSION['groupuser'] as $key => $elem) {
        if (((int)$elem['user'] === (int)$chkUser) && ((int)$elem['status'] === 0)) {
            $_SESSION['groupuser'][$key]['status'] = 1;
        }
    }
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>None</title>
    <link href="<?php
    echo $_SESSION['SETS']['path']['base_url']; ?>config/main.css" rel="stylesheet" type="text/css">
    <script type="text/javascript" language="javascript">
        <!--
        function doDel(key) {
            document.location.href = "<?php
                echo $_SESSION['SETS']['path']['base_url']; ?>admin/groupusers.php?dataId=<?php
                echo $chkDataId; ?>&mode=del&user=" + key;
        }

        //-->
    </script>
</head>
<body style="margin:0">
<table cellpadding="0" cellspacing="0" border="0" width="100%">
    <?php
    if (isset($_SESSION['groupuser']) && is_array($_SESSION['groupuser']) && (count($_SESSION['groupuser']) !== 0)) {
        foreach ($_SESSION['groupuser'] as $elem) {
            if ((int)$elem['status'] === 0) {
                $strUser = $myDBClass->getFieldData('SELECT `username` FROM `tbl_user` WHERE `id`=' . $elem['user']); ?>
                <tr>
                    <td class="tablerow" style="padding-bottom:2px; width:260px"><?php echo $strUser; ?></td>
                    <td class="tablerow" style="padding-bottom:2px; width:260px"><?php
                        echo htmlspecialchars(stripslashes($elem['rights']), ENT_COMPAT, 'UTF-8'); ?></td>
                    <td class="tablerow" style="width:50px" align="right"><img src="<?php
                        echo $_SESSION['SETS']['path']['base_url']; ?>images/delete.gif" width="18" height="18"
                                                                               alt="<?php
                                                                               echo translate('Delete'); ?>"
                                                                               title="<?php echo translate('Delete'); ?>"
                                                                               onClick="doDel('<?php
                                                                               echo $elem['user']; ?>')"
                                                                               style="cursor:pointer"></td>
                </tr>
                <?php
            }
        }
    } else {
        ?>
        <tr>
            <td class="tablerow"><?php echo translate('No data'); ?></td>
            <td class="tablerow">&nbsp;</td>
            <td class="tablerow" align="right">&nbsp;</td>
        </tr>
        <?php
    }
    ?>
</table>
</body>
</html>