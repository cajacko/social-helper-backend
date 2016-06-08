<?php

function is_twitter_class($connection)
{
    if (!is_a($connection, 'SocialHelper\Twitter\Twitter')) {
        return false;
    }

    return true;
}

function is_twitter_app_connection($connection)
{
    if (!is_twitter_class($connection)) {
        return false;
    }

    if ('app' == $connection->getConnectionType()) {
        return false;
    }

    return true;
}

function is_twitter_connection($connection)
{

}
