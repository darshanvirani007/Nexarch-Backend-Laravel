<?php
namespace App\Models;
class Task extends OwnedModel { protected function casts(): array { return ['is_completed'=>'boolean','display_order'=>'integer']; } }
