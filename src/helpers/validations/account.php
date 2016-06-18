<?php

function is_accounts($accounts)
{
    if (!$accounts) {
        return true;
    }

    if (!isset($accounts[0])) {
        return false;
    }

    return is_account($accounts[0]);
}

function is_account($account)
{
    if (!is_a($account, 'SocialHelper\Account\Account')) {
        return false;
    }

    return true;
}

function is_raw_account($account) {
    return true;
}
