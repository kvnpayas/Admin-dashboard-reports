<?php

namespace App\Models;

use App\Models\Reports;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReportCustom extends Model
{
    use HasFactory;

    protected $fillable =[
      'report_id',
      'row_id',
      'col',
      'span',
      'get_latest',
    ];
    public function report(){
      return $this->belongsTo(Reports::class, 'report_id');
    }
}
