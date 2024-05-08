<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Models\Activity;

class LogActivity extends Activity
{
    use HasFactory;

    /**
     * Scope a query to only include search
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $value)
    {
        return $query->where('description', 'LIKE', '%' . $value . '%')->orWhere('properties', 'LIKE', '%' . $value . '%');
    }
}
