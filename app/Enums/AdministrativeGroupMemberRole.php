<?php

namespace App\Enums;

use App\Traits\HasEnumUtilities;

enum AdministrativeGroupMemberRole: string
{
  use HasEnumUtilities;

  case Administrator = 'administrator';
  case Contributor = 'contributor';
}
