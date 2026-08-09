<?php
namespace App\Models;
class DailyTask extends OwnedModel { protected function casts(): array { return ['task_date'=>'date:Y-m-d','is_completed'=>'boolean','display_order'=>'integer']; } }
