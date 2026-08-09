<?php
namespace App\Models;
class BusinessSocialLink extends OwnedModel { protected function casts(): array { return ['is_active'=>'boolean','display_order'=>'integer']; } }
