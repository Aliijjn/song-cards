<?php

namespace App\Services;

class ColorService
{
    public static array $colors = [
        '#C48A93', // dusty rose
        '#8AA5C4', // muted blue
        '#C4B48A', // warm sand
        '#9B8AC4', // soft purple
        '#8AC4B0', // muted teal
        '#C49B8A', // clay
        '#8AC0C4', // blue teal
        '#B1C48A', // olive
        '#C48AAE', // mauve
        '#8AAEC4', // steel blue
        '#C4BE8A', // khaki
        '#A88AC4', // lavender
        '#8AC49D', // sage green
        '#C48F8A', // terracotta
        '#8AB7C4', // ocean
        '#AFC48A', // soft lime
        '#C48AA0', // rosewood
        '#8A96C4', // periwinkle
        '#C4AD8A', // camel
        '#B08AC4', // orchid
        '#8AC4A7', // eucalyptus
        '#C4988A', // brick
        '#8AC4BC', // glacier
        '#BCC48A', // moss
        '#C48ABF', // plum
        '#8AB0C4', // denim
        '#C4B98A', // parchment
        '#BE8AC4', // violet
    ];

    public static function fromString(string $seed): string
    {
        return self::$colors[crc32($seed) % count(self::$colors)];
    }
}
