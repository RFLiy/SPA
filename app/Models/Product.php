<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'category_id',
        'material_id',
        'description',
        'base_price',
        'unit',
        'is_customizable',
        'status',
        'image',
        'stock',
    ];

    protected $casts = [
        'is_customizable' => 'boolean',
        'base_price' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });

        static::addGlobalScope('active', function (Builder $builder) {
            $builder->where('status', 'active')
                ->whereHas('category', function ($query) {
                    $query->where('status', 'active');
                });
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class)->withoutGlobalScopes();
    }

    public function material()
{
    return $this->belongsTo(Material::class, 'material_id');
}

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
