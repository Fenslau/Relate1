<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\MassPrunable;

class Toppost extends Model
{
    use HasFactory;
    use MassPrunable;

    protected $guarded = [];

    public function prunable()
    {
        return static::where('action_time', '<', date('U')-50*24*60*60);
    }
}
