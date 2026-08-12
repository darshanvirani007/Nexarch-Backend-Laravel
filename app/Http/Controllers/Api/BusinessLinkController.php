<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller; use App\Models\Business; use App\Models\BusinessLink; use Illuminate\Http\JsonResponse; use Illuminate\Http\Request;
class BusinessLinkController extends Controller {
 private function business(Request $r,string $id):Business{return Business::ownedBy($r->attributes->get('user_id'))->findOrFail($id);}
 private function rules():array{return ['link_type'=>['required','string','max:50','regex:/^(website|email|admin|hosting|domain|analytics|business-suite|github|other|custom:[a-z0-9][a-z0-9 _-]*)$/i'],'name'=>['required','string','max:120'],'url'=>['required','url:http,https','max:2048'],'show_on_card'=>['sometimes','boolean'],'display_order'=>['sometimes','integer','min:0'],'is_active'=>['sometimes','boolean']];}
 public function index(Request $r,string $business):JsonResponse{return response()->json($this->business($r,$business)->links()->get());}
 public function store(Request $r,string $business):JsonResponse{$b=$this->business($r,$business);$x=$b->links()->create($r->validate($this->rules())+['user_id'=>$r->attributes->get('user_id')]);return response()->json($x,201);}
 public function update(Request $r,string $business,string $link):JsonResponse{$b=$this->business($r,$business);$x=$b->links()->whereKey($link)->firstOrFail();$x->update($r->validate(collect($this->rules())->map(fn($v)=>array_merge(['sometimes'],$v))->all()));return response()->json($x->fresh());}
 public function destroy(Request $r,string $business,string $link):JsonResponse{$this->business($r,$business)->links()->whereKey($link)->firstOrFail()->delete();return response()->json(null,204);}
}
