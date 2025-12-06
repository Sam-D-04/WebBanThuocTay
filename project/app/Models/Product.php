<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'price',
        'stock',
        'image',
        'discount_percent'
    ];

    protected $casts = [
        'price' => 'float',
        'stock' => 'integer',
        'discount_percent' => 'float'
    ];

    protected $appends = ['image_url', 'price_after_discount'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return null;
        }

        return Storage::disk('public')->url($this->image);
    }

    public function getPriceAfterDiscountAttribute()
    {
        if (!$this->discount_percent || $this->discount_percent <= 0) {
            return $this->price;
        }

        return round($this->price * (100 - $this->discount_percent) / 100);
    }

    public function orderItems()
    {
        return $this->hasMany(\App\Models\OrderItem::class);
    }
}
