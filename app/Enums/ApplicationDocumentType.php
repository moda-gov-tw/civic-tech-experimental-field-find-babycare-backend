<?php

namespace App\Enums;

use App\Traits\HasEnumUtilities;

enum ApplicationDocumentType: string
{
  use HasEnumUtilities;

  case HouseholdRegistration = 'household-registration';
  case IncomeTaxAssessmentNotice = 'income-tax-assessment-notice';
  case DisadvantagedFamilyProof = 'disadvantaged-family-proof';
  case AlienResidentCertificate = 'alien-resident-certificate';
}
