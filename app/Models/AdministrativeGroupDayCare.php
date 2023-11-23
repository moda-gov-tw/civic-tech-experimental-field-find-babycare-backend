<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class AdministrativeGroupDayCare extends Pivot
{
  use HasFactory;

  protected $table = 'administrative_group_day_care';
}
