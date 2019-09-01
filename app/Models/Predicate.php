<?php

namespace Astral\Models;

use Illuminate\Database\Eloquent\Model;

class Predicate extends Model
{
    protected $fillable = ['name', 'body'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($predicate) {
            $predicate->user_id = auth()->id();
            $predicate->sort_order = self::where('user_id', auth()->id())->count();
        });
    }
}