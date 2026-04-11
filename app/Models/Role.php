<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    /**
     * The users that belong to this role (Many-to-Many).
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'role_user');
    }
}