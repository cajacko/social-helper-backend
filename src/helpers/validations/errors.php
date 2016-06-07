<?php

function is_error($error)
{
    if (!isset($error['error'])) {
        return false;
    }

    if (!$error['error']) {
        return false;
    }

    return true;
}
