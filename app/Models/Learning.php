<?php
namespace App\Models;
class Learning extends OwnedModel { protected $table='learning'; protected function casts(): array { return ['display_order'=>'integer']; } }
