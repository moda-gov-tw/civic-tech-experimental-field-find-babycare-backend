<?php

namespace App\Enums;

use App\Traits\HasEnumUtilities;

enum DayCareType: string
{
  use HasEnumUtilities;

  case Public = 'public';
  case SemiPublic = 'semi-public';
  case Private = 'private';
}
