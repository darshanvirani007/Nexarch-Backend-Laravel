<?php

namespace App\Database;

use Illuminate\Database\PostgresConnection;

class SupabasePostgresConnection extends PostgresConnection
{
    /**
     * Supavisor transaction mode uses emulated prepares and PostgreSQL does
     * not accept Laravel's default integer representation for boolean values.
     * Keep booleans as PostgreSQL-compatible string literals before the base
     * connection formats dates and other binding types.
     */
    public function prepareBindings(array $bindings): array
    {
        foreach ($bindings as $key => $value) {
            if (is_bool($value)) {
                $bindings[$key] = $value ? 'true' : 'false';
            }
        }

        return parent::prepareBindings($bindings);
    }
}
