<?php

namespace App\Models\Stream;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Projects extends Model
{
    use HasFactory;


      public function links() {
          return $this->hasMany(Links::class, 'project_name', 'project_name');
      }
}
