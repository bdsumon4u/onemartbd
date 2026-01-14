<?php

namespace App\Enums;

enum MediaType: int
{
    case Original = 0;
    case Square800 = 1;
    case Thumb180 = 2;
    case Banner1110x280 = 3;

    public function fileSuffix(): string
    {
        return match ($this) {
            self::Original => '',
            self::Square800 => '_800x800',
            self::Thumb180 => '_180x180',
            self::Banner1110x280 => '_1110x280',
        };
    }

    /**
     * @return array{width:int,height:int}|null
     */
    public function dimensions(): ?array
    {
        return match ($this) {
            self::Original => null,
            self::Square800 => ['width' => 800, 'height' => 800],
            self::Thumb180 => ['width' => 180, 'height' => 180],
            self::Banner1110x280 => ['width' => 1110, 'height' => 280],
        };
    }
}
