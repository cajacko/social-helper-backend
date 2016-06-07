<?php

function is_error($error)
{
    if (!is_a($error, 'SocialHelper\Error\Error')) {
        return false;
    }

    return true;
}
