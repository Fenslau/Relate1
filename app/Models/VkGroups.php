<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VkGroups extends Model
{
    use HasFactory;

    protected $fillable = ['group_id', 'name', 'city', 'members_count', 'type', 'wall', 'site', 'verified', 'market', 'is_closed', 'contacts', 'public_date_label', 'start_date', 'finish_date', 'tags'];

}
