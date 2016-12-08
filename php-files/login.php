<?php

require_once("config.php");
error_reporting(E_ALL);
ini_set('display_errors', 'on');


function login()
{
    global $ldaphost;
    global $baseDN;
    global $groupDN;

    $username = $_SERVER['PHP_AUTH_USER'];
    $password = $_SERVER['PHP_AUTH_PW'];

    $con = @ldap_connect($ldaphost);
    if (!$con)
    {
        echo "ldap_connect failed to ".$ldaphost;
        return 0;
    }

    //------------------ Look for user common name
    $attributes = array('cn', 'mail');
    $dn         = 'ou=People,'.$baseDN;
    $filter     = '(uid='.$username.')';

    $sr = @ldap_search($con, $dn, $filter, $attributes);
    if (!$sr)
    {
        echo "ldap_search failed for dn=".$dn.": ".ldap_error($con);
        return 0;
    }

    $srData = @ldap_get_entries($con, $sr);
    if ($srData["count"]==0)
    {
        echo "No results returned by ldap_get_entries for dn=".$dn.".";
        return 2;
    }

    $email         =$srData[0]['mail'][0];
    $userCommonName=$srData[0]['cn'][0];
    $userDN        =$srData[0]['dn'];

    //------------------ Authenticate user
    if (!@ldap_bind($con, $userDN, $password))
    {
        echo "ldap_bind failed: ".ldap_error($con);
        return 2;
    }

    //------------------ Check if the user is in FACT ldap group
    $attributes= array("member");
    $filter= '(objectClass=*)';

    // Get all members of the group.
    $sr = @ldap_read($con, $groupDN, $filter, $attributes);
    if (!$sr)
    {
        echo "ldap_read failed for dn=".$groupDN.": ".ldap_error($con);
        return 0;
    }

    // retrieve the corresponding data
    $srData = @ldap_get_entries($con, $sr);
    if ($srData["count"]==0)
    {
        echo "No results returned by ldap_get_entries for dn=".$dn.".";
        return 0;
    }

    @ldap_unbind($con);

    $found = false;
    foreach ($srData[0]['member'] as $member)
        if (strpos($member, "cn=".$userCommonName.",")===0)
        {
            echo "You are logged in.";
            return 1;
        }

    echo "Sorry, your credentials don't match!";
    return 2;
}


if ((!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])) && $_POST["button"]=="yes")
{
    header('WWW-Authenticate: Basic realm="QLA"');
    header('HTTP/1.1 401 Unauthorized');
    return 0;
}

$val=login();
if ($val==2 && $_POST["button"]=="yes")
{
    header('WWW-Authenticate: Basic realm="QLA"');
    header('HTTP/1.1 401 Unauthorized');
    return 0;
}
else
    return $val;

login();


?>
