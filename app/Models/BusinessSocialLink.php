<?php

namespace App\Models;

class BusinessSocialLink extends OwnedModel
{
    protected function casts(): array
    {
        return [
            'show_on_card' => 'boolean',
            'is_active' => 'boolean',
            'display_order' => 'integer',
        ];
    }
}
