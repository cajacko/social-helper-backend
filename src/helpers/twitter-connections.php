<?php

function get_twitter_connection_for_query($tracking_query_ID)
{
    $accounts = get_tracking_query_accounts($tracking_query_ID);

    if (is_error($accounts)) {
        return $accounts;
    }

    if (!$accounts) {
        $error = new SocialHelper\Error\Error();
        return $error;
    }

    if (!isset($accounts[0])) {
        $error = new SocialHelper\Error\Error();
        return $error;
    }

    $account = $accounts[0];

    if (!is_account($account)) {
        $error = new SocialHelper\Error\Error();
        return $error;
    }

    $account_twitter_connection_details = $account->getTwitterConnectionDetails();

    if (is_error($account_twitter_connection_details)) {
        return $account_twitter_connection_details;
    }

    if (!isset($account_twitter_connection_details['key'])) {
        $error = new SocialHelper\Error\Error();
        return $error;
    }

    if (!isset($account_twitter_connection_details['secret'])) {
        $error = new SocialHelper\Error\Error();
        return $error;
    }

    $twitter_connection = new SocialHelper\Twitter\Twitter($account_twitter_connection_details);

    if (is_error($twitter_connection)) {
        return $twitter_connection;
    }

    if (!is_twitter_connection($twitter_connection)) {
        $error = new SocialHelper\Error\Error();
        return $error;
    }

    return $twitter_connection;
}
