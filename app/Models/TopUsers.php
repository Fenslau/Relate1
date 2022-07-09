<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TopUsers extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function prunable()
    {
        return static::where('updated_at', '<=', now()->subDays(180));
    }    
}
