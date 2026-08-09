<?php
namespace App\Models;
class BusinessDevelopmentKey extends OwnedModel { protected $hidden=['vault_secret_id']; protected function casts(): array { return ['is_active'=>'boolean']; } }
