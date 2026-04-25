<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApiProvider extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'config' => 'array',
            'active' => 'boolean',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function scopeWhereCompany($query)
    {
        return $query->where('api_providers.company_id', request()->header('company'));
    }

    public static function createFromRequest($request)
    {
        return self::create(array_merge($request->validated(), [
            'company_id' => request()->header('company'),
        ]));
    }

    public function updateFromRequest($request)
    {
        return $this->update($request->validated());
    }
}
