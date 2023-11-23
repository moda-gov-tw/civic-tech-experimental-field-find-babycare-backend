<?php

namespace App\Enums;

use App\Traits\HasEnumUtilities;

enum DayCareMemberRole: string
{
  use HasEnumUtilities;

  case Administrator = 'administrator';
  case Contributor = 'contributor';
}
