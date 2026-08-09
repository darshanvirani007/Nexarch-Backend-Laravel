<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
class Business extends OwnedModel {
 protected function casts(): array { return ['is_archived'=>'boolean','display_order'=>'integer']; }
 public function links(): HasMany { return $this->hasMany(BusinessLink::class)->orderBy('display_order'); }
 public function socialLinks(): HasMany { return $this->hasMany(BusinessSocialLink::class)->orderBy('display_order'); }
 public function developmentKeys(): HasMany { return $this->hasMany(BusinessDevelopmentKey::class); }
 public function note(): HasOne { return $this->hasOne(BusinessNote::class); }
 public function websiteChecks(): HasMany { return $this->hasMany(WebsiteCheck::class)->latest('checked_at'); }
}
