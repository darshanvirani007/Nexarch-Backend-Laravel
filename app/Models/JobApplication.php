<?php
namespace App\Models;
class JobApplication extends OwnedModel { protected function casts(): array { return ['display_order'=>'integer']; } }
