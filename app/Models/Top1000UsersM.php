<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Top1000UsersM extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function prunable()
    {
        return static::where('action_time', '<', date('U')-30*24*60*60);
    }
}
