<?php
namespace App\Models;
class MyLink extends OwnedModel { protected $table='my_links'; protected function casts(): array { return ['is_active'=>'boolean','display_order'=>'integer']; } }
