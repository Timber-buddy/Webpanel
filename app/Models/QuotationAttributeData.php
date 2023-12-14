<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuotationAttributeData extends Model
{
    protected $table = 'quotation_attribute_data';

    protected $fillable = [
        'quotaton_id', 'attribute_data'
    ];
}
