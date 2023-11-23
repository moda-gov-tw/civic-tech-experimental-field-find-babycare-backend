<?php

namespace App\Enums;

use App\Traits\HasEnumUtilities;

enum ApplicationStatus: string
{
  use HasEnumUtilities;

  case Submitted = 'submitted';
  case ApplicationReturned = 'application-returned';
  case RaffleApproved = 'raffle-approved';
  case WaitlistApproved = 'waitlist-approved';
  case Withdrew = 'withdrew';
  case Accepted = 'accepted';
  case Registered = 'registered';
  case RegistrationReturned = 'registration-returned';
  case Forfeited = 'forfeited';
  case Rejected = 'rejected';
}
