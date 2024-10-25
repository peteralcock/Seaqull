<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'prompt',
        'query',
        'database_id',
    ];


    public function database(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Database::class);
    }

    public function team()
    {
        return $this->hasOneThrough(Team::class, Database::class, 'id', 'id', 'database_id', 'team_id');
    }


}
