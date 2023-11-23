<?php

namespace App\Enums;

use App\Traits\HasEnumUtilities;

enum InfantStatusType: string
{
  use HasEnumUtilities;

  case Disadvantaged = 'disadvantaged';
  case Challenged = 'challenged';
  case Aboriginal = 'aboriginal';
  case UnderAgeParents = 'under-age-parents';
  case BigFamily = 'big-family';
  case Adopted = 'adopted';
  case Immigrant = 'immigrant';
}
