<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * Format response.
 */
class Helpers
{
    public static function autonumber($table, $primary, $prefix)
    {
        $q = DB::table($table)->select(DB::raw('MAX(RIGHT(' . $primary . ',5)) as kd_max'));
        $prx = $prefix;
        if ($q->count() > 0) {
            foreach ($q->get() as $k) {
                $tmp = ((int)$k->kd_max) + 1;
                $kd = $prx . sprintf("%06s", $tmp);
            }
        } else {
            $kd = $prx . "000001";
        }

        return $kd;
    }

    public static function codeTracking()
    {
        return strtoupper(Str::random(8));
    }
}
