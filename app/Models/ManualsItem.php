<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManualsItem extends Model
{
    use HasFactory;

    protected $fillable = ['miid', 'manual_uid', 'name','link', 'file_type', 'file_size'];
}
