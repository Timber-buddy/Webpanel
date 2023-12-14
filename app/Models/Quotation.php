<?php

namespace App\Models;
use App\Models\User;
use App\Models\QuotationAttribute;
use App\Models\QuotationAttributeData;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;

class Quotation extends Model
{
    protected $table = 'quotation';

    protected $fillable = [
        'customer_id', 'seller_id', 'product_id', 'product_name', 'discounted_price','quantity'
    ];

    public function attributes()
    {
        return $this->hasMany(QuotationAttribute::class, 'quotation_id');
    }

    public function attributes_data()
    {
        return $this->hasMany(QuotationAttributeData::class, 'quotaton_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id','seller_id');
    }

    public function product()
    {
        return $this->hasOne(Product::class, 'id','product_id');
    }

}
