<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    public $table = 'products';
    
    protected $guarded = [];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'sizes' => 'array',
        'price' => 'decimal:2',
        'stock' => 'integer',
    ];

    /**
     * Get the category that owns the product.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get sizes as a comma-separated string.
     */
    // public function getSizesStringAttribute()
    // {
    //     return $this->sizes ? implode(', ', $this->sizes) : 'No sizes';
    // }

    /**
     * Check if product has a specific size.
     */
    // public function hasSize($size)
    // {
    //     return $this->sizes && in_array($size, $this->sizes);
    // }

    /**
     * Get available sizes count.
     */
    // public function getSizesCountAttribute()
    // {
    //     return $this->sizes ? count($this->sizes) : 0;
    // }

    /**
     * Scope to filter products by size.
     */
    // public function scopeWithSize($query, $size)
    // {
    //     return $query->whereJsonContains('sizes', $size);
    // }

    /**
     * Scope to filter products with any sizes.
     */
    // public function scopeWithSizes($query)
    // {
    //     return $query->whereNotNull('sizes')
    //                 ->where('sizes', '!=', '[]');
    // }

    /**
     * Scope to filter products without sizes.
     */
    // public function scopeWithoutSizes($query)
    // {
    //     return $query->whereNull('sizes')
    //                 ->orWhere('sizes', '[]');
    // }
}