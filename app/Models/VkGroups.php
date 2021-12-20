<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\MassPrunable;

class VkGroups extends Model
{
    use HasFactory;
    use MassPrunable;

    protected $guarded = [];

    public function prunable()
    {
        return static::where('updated_at', '<=', now()->subDays(180));
    }

}
