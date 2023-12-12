<?php

namespace App\Services;

use App\Enums\ApplicationStatus;
use App\Models\Address;
use App\Models\ApplicationDraft;
use Arr;
use DB;

class ApplicationDraftService
{
  public function updateApplicant(ApplicationDraft $draft, array $validatedApplicant): ?int
  {
    $draft->load('applicant.address');

    $id = null;

    $previousAddress = null;

    $validatedApplicantAddress = Arr::pull($validatedApplicant, 'address');

    if (isset($validatedApplicantAddress)) {
      $validatedApplicant['address_id'] = Address::create($validatedApplicantAddress)->id;
    } else {
      $validatedApplicant['address_id'] = null;

      $previousAddress = $draft->applicant?->address;
    }

    $id = $draft->applicant()->updateOrCreate($validatedApplicant)->id;

    $previousAddress?->delete();

    return $id;
  }

  public function updateInfants(ApplicationDraft $draft, array $validatedInfants)
  {
    $draft->load('infants.address');

    foreach ($validatedInfants as $key => $validatedInfant) {
      $validatedInfantAddress = Arr::pull($validatedInfant, 'address');
      $validatedInfantStatuses = Arr::pull($validatedInfant, 'statuses');

      if (isset($draft->infants[$key])) {
        $previousInfantAddress = null;

        if (isset($validatedInfantAddress)) {
          $validatedInfant['address_id'] = $draft->infants[$key]->address->updateOrCreate($validatedInfantAddress)->id;
        } else {
          $previousInfantAddress = $draft->infants[$key]->address;

          $validatedInfant['address_id'] = null;
        }

        $draft->infants[$key]->update($validatedInfant);

        $previousInfantAddress?->delete();

        $draft->infants[$key]->statuses()->delete();

        $draft->infants[$key]->statuses()->createMany(
          collect($validatedInfantStatuses ?? [])
            ->map(fn ($status) => ['type' => $status])
            ->toArray()
        );
      } else {
        if (isset($validatedInfantAddress)) {
          $validatedInfant['address_id'] = Address::create($validatedInfantAddress)->id;
        }

        $infant = $draft->infants()->create($validatedInfant);

        $infant->statuses()->createMany(
          collect($validatedInfantStatuses ?? [])
            ->map(fn ($status) => ['type' => $status])
            ->toArray()
        );
      }
    }

    foreach ($draft->infants->slice(count($validatedInfants)) as $infant) {
      $infant->delete();

      $infant->address?->delete();
    }
  }

  public function updateContact(ApplicationDraft $draft, array $validated)
  {
  }

  public function updateContacts(ApplicationDraft $draft, array $validatedContacts)
  {
    $draft->load('contacts.address');

    foreach ($validatedContacts as $key => $validatedContact) {
      $validatedContactAddress = Arr::pull($validatedContact, 'address');

      if (isset($draft->contacts[$key])) {
        $previousContactAddress = null;

        if (isset($validatedContactAddress)) {
          $validatedContact['address_id'] = $draft->contacts[$key]->address->updateOrCreate($validatedContactAddress)->id;
        } else {
          $previousContactAddress = $draft->contacts[$key]->address;

          $validatedContact['address_id'] = null;
        }

        $draft->contacts[$key]->update($validatedContact);

        $previousContactAddress?->delete();
      } else {
        if (isset($validatedContactAddress)) {
          $validatedContact['address_id'] = Address::create($validatedContactAddress)->id;;
        }

        $draft->contacts()->create($validatedContact);
      }
    }

    foreach ($draft->contacts->slice(count($validatedContacts)) as $contact) {
      $contact->pivot->delete();

      $contact->delete();

      $contact->address?->delete();
    }
  }

  public function update(ApplicationDraft $draft, array $validated)
  {
    return DB::transaction(function () use ($draft, $validated) {
      $validatedApplicant = Arr::pull($validated, 'applicant');
      $validatedInfants = Arr::pull($validated, 'infants');
      $validatedContacts = Arr::pull($validated, 'contacts');

      if (isset($validatedApplicant)) {
        $validated['contact_id'] = $this->updateApplicant($draft, $validatedApplicant);
      }

      if (isset($validatedInfants)) {
        $this->updateInfants($draft, $validatedInfants);
      }

      if (isset($validatedContacts)) {
        $this->updateContacts($draft, $validatedContacts);
      }

      $draft->update($validated);
    });
  }

  public function submit(ApplicationDraft $draft)
  {
    return DB::transaction(function () use ($draft) {
      $draft = ApplicationDraft::where('id', $draft->id)
        ->with([
          'applicant',
          'contacts.address',
          'daycares',
          'infants',
          'documents',
          'dayCareDocuments',
          'infantDocuments'
        ])
        ->lockForUpdate()
        ->first();

      foreach ($draft->infants as $infant) {
        foreach ($draft->dayCares as $dayCare) {
          $application = $draft->user->applications()->create([
            'day_care_id' => $dayCare->id,
            'infant_id' => $infant->id,
            'status' => ApplicationStatus::Submitted,
            'contact_id' => $draft->contact_id
          ]);

          $application->documents()->createMany(
            $draft->documents
              ->map
              ->only('type', 'path')
          );

          $application->dayCareDocuments()->createMany(
            $draft->dayCareDocuments
              ->filter(fn ($dayCareDocument) => $dayCareDocument->day_care_id === $dayCare->id)
              ->map
              ->only('type', 'path')
          );

          $application->infantDocuments()->createMany(
            $draft->infantDocuments
              ->filter(fn ($infantDocument) => $infantDocument->infant_id === $infant->id)
              ->map
              ->only('type', 'path')
          );

          $application->contacts()->attach($draft->contacts);
        }
      }

      $draft->contacts()->detach();

      $draft->delete();
    });
  }

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
