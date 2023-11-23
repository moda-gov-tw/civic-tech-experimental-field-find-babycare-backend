<?php

namespace Database\Factories\Helpers;

use Illuminate\Http\UploadedFile;

class Document
{
  public static function create()
  {
    $file = UploadedFile::fake()->image('document.jpg');

    return $file->store('documents');
  }
}
