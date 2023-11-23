<?php

namespace App\Enums;

use App\Traits\HasEnumUtilities;

enum ApplicationInfantDocumentType: string
{
  use HasEnumUtilities;

  case ChallengedMedicalProof = 'challenged-medical-proof';
  case AdoptionProof = 'adoption-proof';
  case SpecialMedicalConditionDiagnosis = 'special-medical-condition-diagnosis';
}
