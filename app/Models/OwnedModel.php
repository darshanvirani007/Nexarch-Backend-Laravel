<?php

namespace App\Models;

use App\Models\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

abstract class OwnedModel extends Model
{
    use UsesUuid;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function scopeOwnedBy(Builder $query, string $userId): Builder
    {
        return $query->where($this->qualifyColumn('user_id'), $userId);
    }
}
