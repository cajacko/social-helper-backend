<?php

function parse_twitter_query($query)
{
    $array = array('query' => $query);
    return $array;
}

function return_tweet_array($tweets)
{
    global $config, $db;

    $array = array();

    foreach ($tweets as $tweet) {
        $object = new \SocialHelper\Object\SocialObject($config, $db);
        $object->defineTweet($tweet);
        $array[] = $object;
    }

    return $array;
}

function get_twitter_objects_by_reponse($tweet_response)
{
    if (isset($tweet_response->statuses) && count($tweet_response->statuses)) {
        $array = return_tweet_array($tweet_response->statuses);
    } else if(is_array($tweet_response) && count($tweet_response)) {
        $array = return_tweet_array($tweet_response);
    }

    return $array;
}
