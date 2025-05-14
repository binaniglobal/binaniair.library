<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ManualsItem extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['miid', 'manual_uid', 'name', 'link', 'file_type', 'file_size'];

    protected $primaryKey = 'miid'; // Set 'miid' as the primary key

    public $incrementing = false;

    protected $keyType = 'string';

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->miid = Str::uuid();
        });
    }

    public function manual()
    {
        return $this->belongsTo(Manuals::class, 'manual_uid', 'mid');
    }

    public function contents()
    {
        return $this->hasMany(ManualItemContent::class, 'manual_items_uid', 'miid');
    }
}
