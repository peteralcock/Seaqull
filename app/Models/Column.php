<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Column extends Model
{
    protected $fillable = [
        'name',
        'description',
        'table_id',
        'type',
        'character_set',
    ];

    public function table(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Table::class);
    }
}
