<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceCategory extends Model
{
    use HasFactory;

    protected $table = 'service_categories';

    protected $fillable = [
        'name',
        'description',
        'image',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the services for the category.
     */
    public function services()
    {
        return $this->hasMany(Service::class, 'category_id');
    }

    /**
     * Get the tags associated with this service category.
     */
    public function tags()
    {
        return $this->belongsToMany(ServiceCategoryTag::class, 'service_category_tag_pivot', 'service_category_id', 'service_category_tag_id');
    }

    /**
     * Scope to get only active categories
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
