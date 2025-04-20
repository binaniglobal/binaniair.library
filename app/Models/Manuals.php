<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\Concerns\Has;

class Manuals extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['mid', 'name', 'no_of_items', 'status'];
    protected $primaryKey = 'mid'; // Set 'mid' as the primary key
    public $incrementing = false; // Tell Laravel that the primary key is not auto-incrementing
    protected $keyType = 'string'; // Specify the data type of the primary key

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->mid = Str::uuid();
        });
    }

    public function items()
    {
        return $this->hasMany(ManualsItem::class, 'manual_uid', 'mid');
    }
}
