<?php

function get_tracking_query_accounts($tracking_query_ID)
{
    global $db;
    $accounts = $db->getTrackingQueryAccounts($tracking_query_ID);
    $array = array();

    foreach ($accounts as $account) {
        $account = new \SocialHelper\Account\Account($account);
        $array[] = $account;
    }

    return $array;
}
