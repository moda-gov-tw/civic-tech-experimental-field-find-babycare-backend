<?php

namespace App\Enums;

use App\Traits\HasEnumUtilities;

enum InfantSex: string
{
  use HasEnumUtilities;

  case Male = 'male';
  case Female = 'female';
}
