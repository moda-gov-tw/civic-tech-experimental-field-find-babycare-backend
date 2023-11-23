<?php

namespace App\Enums;

use App\Traits\HasEnumUtilities;

enum DayCareCategory: string
{
  use HasEnumUtilities;

  case Center = 'center';
  case Home = 'home';
}
