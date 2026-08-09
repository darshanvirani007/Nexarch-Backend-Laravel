<?php
namespace App\Models;
class Goal extends OwnedModel { protected function casts(): array { return ['deadline'=>'date:Y-m-d','current_value'=>'decimal:2','target_value'=>'decimal:2','display_order'=>'integer']; } }
