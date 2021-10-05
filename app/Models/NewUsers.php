<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewUsers extends Model
{
    use HasFactory;
    protected $fillable = ['vkid', 'group_id',	'name',	'uid1',	'uid2'];
}
