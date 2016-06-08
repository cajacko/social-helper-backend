<?php

function is_account($account)
{
    if (!is_a($account, 'SocialHelper\Account\Account')) {
        return false;
    }

    return true;
}
