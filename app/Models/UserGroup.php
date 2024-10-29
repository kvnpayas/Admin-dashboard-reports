<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserGroup extends Model
{
    use HasFactory;

    protected $fillable = [
      'name',
      'description',
    ];

    public function reports()
    {
      return $this->belongsToMany(Reports::class, 'group_reports', 'group_id', 'report_id');
    }

    public function users()
    {
      return $this->hasMany(User::class, 'group_id');
    }
}
