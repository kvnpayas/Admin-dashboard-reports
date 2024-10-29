<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportRow extends Model
{
    use HasFactory;

    protected $fillable = [
      'user_id',
      'row',
      'col',
      'add',
    ];

    public function reportCols(){
      return $this->hasMany(ReportCustom::class, 'row_id');
    }
}
