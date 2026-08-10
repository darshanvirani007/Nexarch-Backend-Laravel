<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;

class MyLink extends OwnedModel
{
    protected $table = 'my_links';

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'display_order' => 'integer'];
    }

    /**
     * The frontend sends display labels such as "GitHub" and "YouTube",
     * while my_links_type_check stores canonical lowercase identifiers.
     */
    protected function linkType(): Attribute
    {
        return Attribute::make(
            set: static fn (string $value): string => strtolower(trim($value)),
        );
    }
}
