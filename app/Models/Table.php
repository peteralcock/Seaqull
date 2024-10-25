<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    protected $fillable = ['name', 'description', 'database_id'];

    public function database(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Database::class);
    }

    public function columns(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Column::class);
    }
}
