<?php

function parse_twitter_query($query)
{
    $array = array('query' => $query);

    return $array;
}

function get_twitter_objects_by_reponse($tweet_response)
{
    global $config, $db;

    $array = array();

    if (isset($tweet_response->statuses) && count($tweet_response->statuses)) {
        foreach ($tweet_response->statuses as $tweet) {
            $object = new \SocialHelper\Object\SocialObject($config, $db);
            $object->defineTweet($tweet);
            $array[] = $object;
        }
    }



    return $array;
}
