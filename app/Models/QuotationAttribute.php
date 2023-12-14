<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuotationAttribute extends Model
{
    protected $table = 'quotation_attribute';

    protected $fillable = [
        'quotation_id', 'attribute'
    ];
}
