<?php

namespace App\Services;

use App\Models\ApplicationDraft;

class ApplicationDraftService
{

  // Draft Identity assigned when getting to step 3 in draft.
  // def assign_identities(daycare_infos, infant):
  //   identities = []

  //   for daycare_info in daycare_infos:
  //       # Apply identity assignment logic for each daycare
  //       if daycare_info['publicReservedQualification'] and daycare_info['location'] in ['publicHousing', 'school']:
  //           identities.append('A-1')
  //       elif daycare_info['publicReservedQualification'] and daycare_info['location'] == 'neighborhoodCenter':
  //           identities.append('A-2')
  //       elif 'B' in infant['identities']: # identities added through infants in step 1
  //           identities.append('B')
  //       elif 'C' in infant['identities']:
  //           identities.append('C')
  //       elif 'D' in infant['identities']:
  //           identities.append('D')
  //       elif 'E' in infant['identities']:
  //           identities.append('E')
  //       elif 'F' in infant['identities']:
  //           identities.append('F')
  //       elif 'G' in infant['identities']:
  //           identities.append('G')
  //       elif daycare_info['employeeReservedQualification']:
  //           identities.append('H')
  //       elif infant['identities'] == 'non' and infant['multipleBirth']:
  //           identities.append('I')
  //       elif infant['identities'] == 'non' and not infant['multipleBirth']:
  //           identities.append('J')
  //       elif 'K' in infant['identities']:
  //           identities.append('K')
  //       elif infant['specialCondition']: # checkbox in the ui
  //           identities.append('L')

  //   return identities
}
