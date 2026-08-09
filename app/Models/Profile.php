<?php
namespace App\Models;
use App\Models\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Model;
class Profile extends Model { use UsesUuid; protected $guarded=['created_at','updated_at']; }
