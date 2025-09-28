<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceCategoryTag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the service categories that have this tag.
     */
    public function serviceCategories()
    {
        return $this->belongsToMany(ServiceCategory::class, 'service_category_tag_pivot', 'service_category_tag_id', 'service_category_id');
    }

    /**
     * Scope to get only active tags
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
