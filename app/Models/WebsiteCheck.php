<?php
namespace App\Models;
class WebsiteCheck extends OwnedModel { public $timestamps=false; protected function casts(): array { return ['checked_at'=>'datetime','http_status_code'=>'integer','response_time_ms'=>'integer']; } }
