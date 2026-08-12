<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;

class BusinessLink extends OwnedModel
{
    protected function casts(): array
    {
        return ['show_on_card'=>'boolean','is_active'=>'boolean','display_order'=>'integer'];
    }

    /**
     * Business-link types are identifiers, not display labels. Keep the
     * values stable for the PostgreSQL check constraint and frontend mapper.
     */
    protected function linkType(): Attribute
    {
        return Attribute::make(
            set: static function (string $value): string {
                $value = strtolower(trim($value));

                if (str_starts_with($value, 'custom:')) {
                    $category = str($value)->after('custom:')->slug()->toString();

                    return 'custom:'.($category ?: 'other');
                }

                return str($value)->slug()->toString();
            },
        );
    }
}
