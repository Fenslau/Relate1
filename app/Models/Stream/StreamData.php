<?php

namespace App\Models\Stream;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\MassPrunable;
use App\Models\Stream\Projects;
use Illuminate\Support\Facades\DB;

class StreamData extends Model
{
    use HasFactory;
    use MassPrunable;

    public function prunable() {

        $rules = Projects::select(DB::raw("CONCAT(vkid, '', rule) AS rules"))->whereNotNull('rule')->pluck('rules')->toArray();

        return static::where([['check_trash', '>', 0], ['cloud', 0]])->orwhereNotIn('user', $rules);
    }

}
