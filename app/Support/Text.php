<?php

namespace App\Support;

use Illuminate\Support\Str;

final class Text
{
    /** Tag blok yang menandai batas paragraf/baris: diganti spasi sebelum tag dibuang. */
    private const BLOCK_TAGS = 'br|hr|p|div|li|ul|ol|h[1-6]|blockquote|pre|tr|td|th|table|section|article|figure|figcaption';

    /**
     * Potongan teks polos dari konten HTML (kartu berita, meta description).
     *
     * strip_tags() saja membuat "14.00 WIB.</p><p>Penghuni" menjadi "WIB.Penghuni".
     * Di sini setiap batas tag blok dan baris baru diganti spasi, entitas HTML
     * diterjemahkan, lalu spasi berlebih dirapikan sebelum dipotong.
     */
    public static function excerpt(?string $html, int $limit = 160): string
    {
        $text = preg_replace('/<\s*\/?\s*(?:' . self::BLOCK_TAGS . ')\b[^>]*>/i', ' ', (string) $html);
        $text = html_entity_decode(strip_tags($text), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = trim(preg_replace('/[\s\x{00A0}]+/u', ' ', $text));

        return Str::limit($text, $limit);
    }
}
