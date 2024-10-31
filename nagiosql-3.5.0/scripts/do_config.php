#!/usr/bin/php
<?php
/* ----------------------------------------------------------------------------
 NagiosQL
-------------------------------------------------------------------------------
 (c) 2005-2023 by Martin Willisegger

 Project   : NagiosQL
 Component : Scripting API
 Website   : https://sourceforge.net/projects/nagiosql/
 Version   : 3.5.0
 GIT Repo  : https://gitlab.com/wizonet/NagiosQL
-----------------------------------------------------------------------------*/

/*
To enable scripting functionality - comment out the line below
*/
exit;
//
use functions\MysqliDbClass;
use functions\NagConfigClass;
use functions\NagDataClass;
use functions\NagImportClass;
use functions\NagVisualClass;

/**
 * Class and variable includes
 * @var MysqliDbClass $myDBClass MySQL database class
 * @var NagVisualClass $myVisClass Visual content class
 * @var NagDataClass $myDataClass NagiosQL data class
 * @var NagImportClass $myImportClass NagiosQL content class
 * @var NagConfigClass $myConfigClass NagiosQL configuration class
 */
/*
Include preprocessing file
*/
$preAccess = 0;
$preNoMain = 1;
require str_replace('scripts', '', __DIR__) . 'functions/prepend_scripting.php';
/*
Process post parameters
*/
$argFunction = isset($argv[1]) ? htmlspecialchars($argv[1], ENT_QUOTES, 'utf-8') : 'none';
$argDomain = isset($argv[2]) ? htmlspecialchars($argv[2], ENT_QUOTES, 'utf-8') : 'none';
$argObject = isset($argv[3]) ? htmlspecialchars($argv[3], ENT_QUOTES, 'utf-8') : 'none';
if ((($argDomain === 'none')) || (($argFunction === 'write') && ($argObject === 'none')) ||
    (($argFunction !== 'write') && ($argFunction !== 'check') && ($argFunction !== 'restart') && ($argFunction !== 'import'))) {
    echo 'Usage: ' . htmlspecialchars($argv[0], ENT_QUOTES, 'utf-8') . " function domain [object]\n";
    echo "function = write/check/restart/import\n";
    echo "domain   = domain name like 'localhost'\n";
    echo "object   = object name, see below:\n";
    echo "import: object = file name like 'hostgroups.cfg' or 'localhost.cfg'\n";
    echo "write:  object = table name like 'tbl_contact' or simplier 'contact' without 'tbl_'\n";
    echo "Attention: import function replaces existing data!\n";
    echo "Note that the new backup and configuration files becomes the UID/GID\nfrom the calling user and probably ";
    echo "can't be deleted via web GUI anymore!\n";
    exit(1);
}
/*
Get domain ID
*/
$strSQL = "SELECT `targets` FROM `tbl_datadomain` WHERE `domain`='$argDomain'";
$intTarget = $myDBClass->getFieldData($strSQL);
$strSQL = "SELECT `id` FROM `tbl_datadomain` WHERE `domain`='$argDomain'";
$intDomain = $myDBClass->getFieldData($strSQL);
if ($intDomain === '') {
    echo "Domain '" . $argDomain . "' doesn not exist\n";
    exit(1);
}
if ($intDomain === '0') {
    echo "Domain '" . $argDomain . "' cannot be used\n";
    exit(1);
}
$myDataClass->intDomainId   = $intDomain;
$myConfigClass->intDomainId = $intDomain;
$myImportClass->intDomainId = $intDomain;
$strMethod = '';
$intMethod = 0;
/* Get connection method */
if ($myConfigClass->getConfigData($intTarget, 'method', $strMethod) === 0) {
    $intMethod = (int)$strMethod;
}
/*
Process form variables
*/
if ($argFunction === 'check') {
    $myConfigClass->getConfigData($intTarget, 'binaryfile', $strBinary);
    $myConfigClass->getConfigData($intTarget, 'basedir', $strBaseDir);
    $myConfigClass->getConfigData($intTarget, 'nagiosbasedir', $strNagiosBaseDir);
    $myConfigClass->getConfigData($intTarget, 'conffile', $strConffile);
    if ($intMethod === 1) {
        if (file_exists($strBinary) && is_executable($strBinary)) {
            $resFile = popen($strBinary . ' -v ' . $strConffile, 'r');
        } else {
            echo "Cannot find the Nagios binary or no execute permissions!\n";
            exit(1);
        }
    } elseif ($intMethod === 2) {
        $booReturn = 0;
        if (empty($myConfigClass->conFTPConId)) {
            $booReturn = $myConfigClass->getFTPConnection($intTarget);
        }
        if ($booReturn === 1) {
            $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
        } else {
            $intErrorReporting = error_reporting();
            error_reporting(0);
            if (!($resFile = ftp_exec($myConfigClass->conFTPConId, $strBinary . ' -v ' . $strConffile))) {
                echo "Remote execution (FTP SITE EXEC) is not supported on your system!\n";
                error_reporting($intErrorReporting);
                exit(1);
            }
            ftp_close($myConfigClass->conFTPConId);
            error_reporting($intErrorReporting);
        }
    } elseif ($intMethod === 3) {
        $booReturn = 0;
        if (empty($myConfigClass->resSSHConId) || !is_resource($myConfigClass->resSSHConId)) {
            $booReturn = $myConfigClass->getSSHConnection($intTarget);
        }
        if ($booReturn === 1) {
            echo 'SSH connection failure: ' . str_replace('::', "\n", $myConfigClass->strErrorMessage);
            exit(1);
        }
        $intRet1 = $myConfigClass->sendSSHCommand('ls ' . $strBinary, $arrRet1);
        $intRet2 = $myConfigClass->sendSSHCommand('ls ' . $strConffile, $arrRet2);
        if (($intRet1 === 0) && ($intRet2 === 0) && is_array($arrRet1) && is_array($arrRet2)) {
            $intRet3 = $myConfigClass->sendSSHCommand($strBinary . ' -v ' . $strConffile, $arrResult);
            if ($intRet3 !== 0) {
                echo "Remote execution of nagios verify command failed (remote SSH)!\n";
                exit(1);
            }
        } else {
            echo "Nagios binary or configuration file not found (remote SSH)!\n";
            exit(1);
        }
    }
}
if ($argFunction === 'restart') {
    /* Read config file */
    $myConfigClass->getConfigData($intTarget, 'commandfile', $strCommandfile);
    $myConfigClass->getConfigData($intTarget, 'pidfile', $strPidfile);
    /* Check state nagios demon */
    clearstatcache();
    if ($intMethod === 1) {
        if (file_exists($strPidfile)) {
            if (file_exists($strCommandfile) && is_writable($strCommandfile)) {
                $strCommandString = '[' . time() . '] RESTART_PROGRAM;' . time() . "\n";
                $timeout = 3;
                $old = ini_set('default_socket_timeout', $timeout);
                $resCmdFile = fopen($strCommandfile, 'wb');
                ini_set('default_socket_timeout', $old);
                stream_set_timeout($resCmdFile, $timeout);
                stream_set_blocking($resCmdFile, 0);
                if ($resCmdFile) {
                    fwrite($resCmdFile, $strCommandString);
                    fclose($resCmdFile);
                    echo "Restart command successfully send to Nagios\n";
                    exit(0);
                }
            }
            echo "Restart failed - Nagios command file not found or no execute permissions\n";
            exit(1);
        }
        echo "Nagios daemon is not running, cannot send restart command!\n";
        exit(1);
    }
    if ($intMethod === 2) {
        echo "Nagios restart is not possible via FTP remote connection!\n";
        exit(1);
    }
    if ($intMethod === 3) {
        $booReturn = 0;
        if (empty($myConfigClass->resSSHConId) || !is_resource($myConfigClass->resSSHConId)) {
            $booReturn = $myConfigClass->getSSHConnection($intTarget);
        }
        if ($booReturn === 1) {
            $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
        } else {
            $intRet1 = $myConfigClass->sendSSHCommand('ls ' . $strCommandfile, $arrRet1);
            if (($intRet1 === 0) && is_array($arrRet1)) {
                $strCommandString = '[' . time() . '] RESTART_PROGRAM;' . time() . "\n";
                $strCommand = 'echo "' . $strCommandString . '" >> ' . $strCommandfile;
                $intRet2 = $myConfigClass->sendSSHCommand($strCommand, $arrResult);
                if ($intRet2 !== 0) {
                    echo "Restart failed - Nagios command file not found or no rights to execute (remote SSH)!\n";
                    exit(1);
                }
                echo "Nagios daemon successfully restarted (remote SSH)\n";
                exit(0);
            }
            echo "Nagios command file not found (remote SSH)!\n";
            exit(1);
        }
    }
}
if ($argFunction === 'write') {
    if (substr_count($argObject, 'tbl_') !== 0) {
        $argObject = str_replace('tbl_', '', $argObject);
    }
    if (substr_count($argObject, '.cfg') !== 0) {
        $argObject = str_replace('.cfg', '', $argObject);
    }
    if ($argObject === 'host') {
        /* Write host configuration */
        $strInfo = "Write host configurations  ...\n";
        $strSQL = "SELECT `id` FROM `tbl_host` WHERE `config_id` = $intDomain AND `active`='1'";
        $myDBClass->hasDataArray($strSQL, $arrData, $intDataCount);
        $intError = 0;
        if ($intDataCount !== 0) {
            foreach ($arrData as $data) {
                $intReturn = $myConfigClass->createConfigSingle('tbl_host', $data['id']);
                if ($intReturn === 1) {
                    $intError++;
                }
            }
        }
        if ($intError === 0) {
            $strInfo .= "Host configuration files successfully written!\n";
        } else {
            $strInfo .= "Cannot open/overwrite the configuration file (check the permissions)!\n";
        }
    } elseif ($argObject === 'service') {
        /* Write service configuration */
        $strInfo = "Write service configurations ...\n";
        $strSQL = 'SELECT `id`, `config_name` FROM `tbl_service` '
            . "WHERE `config_id` = $intDomain AND `active`='1' GROUP BY `config_name`";
        $myDBClass->hasDataArray($strSQL, $arrData, $intDataCount);
        $intError = 0;
        if ($intDataCount !== 0) {
            foreach ($arrData as $data) {
                $intReturn = $myConfigClass->createConfigSingle('tbl_service', $data['id']);
                if ($intReturn === 1) {
                    $intError++;
                }
            }
        }
        if ($intError === 0) {
            $strInfo .= "Service configuration file successfully written!\n";
        } else {
            $strInfo .= "Cannot open/overwrite the configuration file (check the permissions)!\n";
        }
    } else {
        $strInfo = 'Write ' . $argObject . ".cfg ...\n";
        $booReturn = $myConfigClass->createConfig('tbl_' . $argObject);
        if ($booReturn === 0) {
            $strInfo .= 'Configuration file ' . $argObject . ".cfg successfully written!\n";
        } else {
            echo $myConfigClass->strErrorMessage;
            $strInfo .= 'Cannot open/overwrite the configuration file ' . $argObject . '.cfg (check the permissions or '
                . 'probably tbl_' . $argObject . " does not exists)!\n";
        }
    }
    echo $strInfo;
}
if ($argFunction === 'import') {
    $strInfo = "Importing configurations ...\n";
    $intReturn = $myImportClass->fileImport($argObject, $intTarget, '1');
    if ($intReturn !== 0) {
        $strInfo .= $myImportClass->strErrorMessage;
    } else {
        $strInfo .= $myImportClass->strInfoMessage;
    }
    $strInfo = strip_tags($strInfo);
    echo str_replace('::', "\n", $strInfo);
}
/*
Output processing
*/
if (isset($resFile) && ($resFile !== false)) {
    $intError = 0;
    $intWarning = 0;
    $strOutput = '';
    while (!feof($resFile)) {
        $strLine = fgets($resFile, 1024);
        if (substr_count($strLine, 'Error:') !== 0) {
            $intError++;
        }
        if (substr_count($strLine, 'Warning:') !== 0) {
            $intWarning++;
        }
        $strOutput .= $strLine;
    }
    pclose($resFile);
    echo $strOutput."\n";
    if (($intError === 0) && ($intWarning === 0)) {
        echo "Written configuration files are valid, Nagios can be restarted!\n\n";
    }
} elseif (isset($arrResult) && is_array($arrResult)) {
    $intError   = 0;
    $intWarning = 0;
    $strOutput  = '';
    foreach ($arrResult as $elem) {
        if (substr_count($elem, 'Error:') !== 0) {
            $intError++;
        }
        if (substr_count($elem, 'Warning:') !== 0) {
            $intWarning++;
        }
        $strOutput .= $elem."\n";
    }
    echo $strOutput."\n";
    if (($intError === 0) && ($intWarning === 0)) {
        echo "Written configuration files are valid, Nagios can be restarted!\n\n";
    }
}