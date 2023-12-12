<?php

namespace App\Enums;

use App\Traits\HasEnumUtilities;

enum InfantStatusType: string
{
  use HasEnumUtilities;

  case DisadvantagedFamily = 'disadvantaged_family';
  case Challenged = 'challenged';
  case Aboriginal = 'aboriginal';
  case UnderAgeParents = 'under_age_parents';
  case BigFamily = 'big_family';
  case Adoption = 'adoption';
  case Immigrant = 'immigrant';
}
