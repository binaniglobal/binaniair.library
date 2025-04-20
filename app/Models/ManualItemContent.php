<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ManualItemContent extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['micd', 'manual_uid', 'manual_items_uid', 'name', 'link', 'file_type', 'file_size'];
    protected $primaryKey = 'micd'; // Set 'micd' as the primary key
    public $incrementing = false;
    protected $keyType = 'string';

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->micd = Str::uuid();
        });
    }

    public function item()
    {
        return $this->belongsTo(ManualsItem::class, 'manual_items_uid', 'miid');
    }
}
