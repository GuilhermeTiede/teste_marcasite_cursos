<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'category', 'price', 'seats',
        'registration_start', 'registration_end',
        'description', 'thumbnail', 'is_active'
    ];

    public function materials()
    {
        return $this->hasMany(CourseMaterial::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class);
    }

}
