<?php

function get_tracking_query_accounts($tracking_query)
{
    global $db, $config;

    $tracking_query_ID = $tracking_query->getID();

    if (!is_numeric($tracking_query_ID) || 0 === $tracking_query_ID) {
        $error = new \SocialHelper\Error\Error();
        return $error;
    }

    $accounts = $db->getTrackingQueryAccounts($tracking_query_ID);

    if (!$accounts) {
        return false;
    }

    if (is_error($accounts)) {
        return $accounts;
    }

    if (!is_array($accounts)) {
        $error = new \SocialHelper\Error\Error();
        return $error;
    }

    $array = array();

    foreach ($accounts as $account) {
        if (!isset($account['id'])) {
            break;
        }

        if (!is_numeric($account['id'])) {
            break;
        }

        $account = new \SocialHelper\Account\Account($db, $config, $account);
        $array[] = $account;
    }

    if (count($array) === 0) {
        return false;
    }

    return $array;
}
