<?php

namespace App\Enums;

use App\Traits\HasEnumUtilities;

enum ApplicationDayCareDocumentType: string
{
  use HasEnumUtilities;

  case ReservedSpotQualificationProof = 'reserved-spot-qualification-proof';
  case DaycareEmploymentProof = 'daycare-employment-proof';
}
