<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    use HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;
    protected $primaryKey = 'uuid';

    public $table = 'roles';

    protected $fillable = ['uuid', 'name', 'guard_name'];

//    protected static function boot(): void
//    {
//        parent::boot();
//        static::creating(function ($role) {
//            $role->uuid = Str::uuid();
//        });
//    }

}
