<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Manuals extends Model
{
    use HasFactory;

    protected $fillable = ['mid', 'name'];
}
