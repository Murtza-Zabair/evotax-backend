<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'contact',
        'address',
        'message',
        'orders',
    ];

    protected $casts = [
        'orders' => 'array',
    ];

    /**
     * Get the total number of items in the order
     */
    public function getTotalItemsAttribute()
    {
        return collect($this->orders)->sum('quantity');
    }

    /**
     * Get unique product categories from orders
     */
    public function getCategoriesAttribute()
    {
        return collect($this->orders)->pluck('category')->unique()->values();
    }
}
