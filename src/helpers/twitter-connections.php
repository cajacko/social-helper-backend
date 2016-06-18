<?php

function get_twitter_connection_for_query($tracking_query)
{
    $accounts = get_tracking_query_accounts($tracking_query);

    if (is_error($accounts)) {
        return $accounts;
    }

    if (!$accounts) {
        $error = new SocialHelper\Error\Error();
        return $error;
    }

    if (!isset($accounts[0])) {
        $error = new SocialHelper\Error\Error(4);
        return $error;
    }

    $account = $accounts[0];

    return get_twitter_connection_for_account($account);
}

function get_twitter_connection_for_account($account)
{
    if (!is_account($account)) {
        $error = new SocialHelper\Error\Error(5);
        return $error;
    }

    $response = $account->connectToTwitter();

    if (is_error($response)) {
        return $response;
    }

    $twitter_connection = $account->twitterConnection;

    if (is_error($twitter_connection)) {
        return $twitter_connection;
    }

    if (!is_twitter_class($twitter_connection)) {
        $error = new SocialHelper\Error\Error(8);
        return $error;
    }

    $response = $twitter_connection->verifySuccessfulConnection();

    if (!$response) {
        $error = new SocialHelper\Error\Error(15);
        return $error;
    }

    if (is_error($response)) {
        return $response;
    }

    return $twitter_connection;
}
