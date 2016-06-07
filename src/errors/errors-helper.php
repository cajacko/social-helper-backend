<?php

function is_error($result = false)
{
    if (!isset($result['error'])) {
        return false;
    }

    if (!$result['error']) {
        return false;
    }

    return true;
}
