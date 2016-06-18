<?php

function get_accounts()
{
    global $db, $config;

    $accounts = $db->getAccounts();
    $array = array();

    if (!$accounts) {
        return false;
    }

    if (!is_array($accounts)) {
        $error = new \SocialHelper\Error\Error();
        return $error;
    }

    foreach ($accounts as $account) {
        if (!is_raw_account($account)) {
            $error = new \SocialHelper\Error\Error();
            return $error;
        }

        if (!isset($account['accountID']) || !is_numeric($account['accountID'])) {
            $error = new \SocialHelper\Error\Error();
            return $error;
        }

        $account_id = $account['accountID'];

        if (!isset($account['type'])) {
            $error = new \SocialHelper\Error\Error();
            return $error;
        }

        $account_type = $account['type'];

        if (!isset($account['UID'])) {
            $error = new \SocialHelper\Error\Error();
            return $error;
        }

        $account_UID = $account['UID'];
        
        $array[] = new SocialHelper\Account\Account($db, $config, $account_id, $account_type, $account_UID);
    }

    return $array;
}
