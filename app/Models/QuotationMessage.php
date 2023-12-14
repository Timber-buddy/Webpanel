<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuotationMessage extends Model
{
    protected $table = 'quotation_message';

    protected $fillable = [
        'quotation_id', 'user_id', 'message', 'status'
    ];
}
