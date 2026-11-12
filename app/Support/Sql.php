<?php

namespace App\Support;

/**
 * Potongan SQL khusus PostgreSQL (Neon).
 */
class Sql
{
    /** Ekspresi "YYYY-MM" dari kolom tanggal. */
    public static function yearMonth(string $column): string
    {
        return "to_char({$column}, 'YYYY-MM')";
    }

    /** Ekspresi tanggal sebagai teks: format 'dmy' = dd-mm-yyyy, 'ymd' = yyyy-mm-dd. */
    public static function dateText(string $column, string $format = 'dmy'): string
    {
        return $format === 'dmy'
            ? "to_char({$column}, 'DD-MM-YYYY')"
            : "to_char({$column}, 'YYYY-MM-DD')";
    }
}
