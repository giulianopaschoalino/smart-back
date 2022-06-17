<?php

if (!function_exists('checkUserId')) {
    function checkUserId($client_id): bool
    {
        return \auth()->hasUser() && !is_null($client_id);
    }
}
