<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller; use App\Models\Business; use Illuminate\Http\JsonResponse; use Illuminate\Http\Request;
class BusinessSocialLinkController extends Controller {
 private function business(Request $r,string $id):Business{return Business::ownedBy($r->attributes->get('user_id'))->findOrFail($id);}
 private function rules():array{return ['platform'=>['required','string','max:50'],'username'=>['nullable','string','max:120'],'url'=>['required','url:http,https','max:2048'],'display_order'=>['sometimes','integer','min:0'],'is_active'=>['sometimes','boolean']];}
 public function store(Request $r,string $business):JsonResponse{$b=$this->business($r,$business);return response()->json($b->socialLinks()->create($r->validate($this->rules())+['user_id'=>$r->attributes->get('user_id')]),201);}
 public function update(Request $r,string $business,string $social):JsonResponse{$x=$this->business($r,$business)->socialLinks()->whereKey($social)->firstOrFail();$x->update($r->validate(collect($this->rules())->map(fn($v)=>array_merge(['sometimes'],$v))->all()));return response()->json($x->fresh());}
 public function destroy(Request $r,string $business,string $social):JsonResponse{$this->business($r,$business)->socialLinks()->whereKey($social)->firstOrFail()->delete();return response()->json(null,204);}
}
