<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    use HasFactory;

    protected $table = 'userroles'; // Ensure this matches your actual table name

    protected $primaryKey = 'roleid'; // Primary key column name
    public $incrementing = true; // Enable auto-increment
    protected $keyType = 'int'; // Primary key is an integer

    // Mass assignable attributes
    protected $fillable = ['codename', 'shortname', 'description'];

    // Timestamps
    public $timestamps = true; // Enable created_at and updated_at
}
