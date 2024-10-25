<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Database extends Model
{
    protected $fillable = [
        'user_id',
        'team_id',
        'host',
        'port',
        'username',
        'password',
        'name',
        'connected_at',
    ];
    protected $casts = [
        'connected_at' => 'datetime',
    ];


    //create global scope
    protected static function booted()
    {
        static::addGlobalScope('team', function ($query) {
            $query->whereIn('team_id', auth()->user()->teams->pluck('id'))->orWhereIn('team_id',auth()->user()->ownedTeams()->pluck('id'));
        });
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function team(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function tables(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Table::class);
    }

    public function reports(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Report::class);
    }
}
