<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tutorial extends Model
{
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function getYouTubeId()
    {
        parse_str(parse_url($this->link, PHP_URL_QUERY), $params);
        return $params['v'] ?? null;
    }
}
