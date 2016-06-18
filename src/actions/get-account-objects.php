<?php

function get_account_objects()
{
    global $db;
    
    $log = new SocialHelper\Logs\Log(2);

    $accounts = get_accounts();

    if (is_error($accounts)) {
        $log->error($accounts);
        $log->save();
        return $accounts; // Tracking query error
    }

    if (!is_accounts($accounts)) {
        $error = new SocialHelper\Error\Error();
        $log->error($error);
        $log->save();
        return false; // Returned in the wrong format
    }

    // If there are tracking queries then process them
    if ($accounts) {
        foreach ($accounts as $account) {
            $objects = get_account_objects_by_account($account);

            if (is_error($objects)) {
                $log->error($objects);
                break;
            }

            if (!is_objects($objects)) {
                $error = new SocialHelper\Error\Error();
                $log->error($error);
                break;
            }

            if ($objects) {
                foreach ($objects as $object) {
                    $object = save_object($object, $account);

                    if (is_error($object)) {
                        $log->error($object);
                        break;
                    }

                    if (!$object) {
                        $error = new SocialHelper\Error\Error();
                        $log->error($error);
                        break;
                    }
                }
            }
        }
    }

    $log->save();
    return true;
}
