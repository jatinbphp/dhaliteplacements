<?php

use Carbon\Carbon;

if (!function_exists('formateDate')) {
    function formateDate($date, $fromFormat = 'm-d-Y', $toFormat = 'Y-m-d')
    {
        if (!$date) {
            return null;
        }

        try {
            return Carbon::createFromFormat($fromFormat, $date)->format($toFormat);
        } catch (\Exception $e) {
            return null;
        }
    }
}
