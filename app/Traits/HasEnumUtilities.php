<?php

namespace App\Traits;

trait HasEnumUtilities
{
  public static function names(): array
  {
    return array_column(self::cases(), 'name');
  }

  public static function values(): array
  {
    return array_column(self::cases(), 'value');
  }
}
