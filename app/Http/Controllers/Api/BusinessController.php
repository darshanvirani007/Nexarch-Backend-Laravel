<?php
namespace App\Http\Controllers\Api;
use App\Models\Business; use App\Models\OwnedModel; use Illuminate\Database\Eloquent\Builder; use Illuminate\Http\JsonResponse; use Illuminate\Http\Request; use Illuminate\Support\Facades\DB;
class BusinessController extends OwnedCrudController {
 protected string $model=Business::class;
 protected function rules(Request $r,?OwnedModel $x=null):array{return ['name'=>['required','string','max:160'],'description'=>['nullable','string','max:2000'],'is_archived'=>['sometimes','boolean'],'display_order'=>['sometimes','integer','min:0']];}
 protected function applyFilters(Builder $q,Request $r):Builder { if($r->has('archived'))$q->where('is_archived',$r->boolean('archived')); else $q->where('is_archived',false); if($r->filled('search'))$q->where(fn($v)=>$v->where('name','ilike','%'.$r->string('search').'%')->orWhere('description','ilike','%'.$r->string('search').'%')); return $q; }
 public function show(Request $r,string $id):JsonResponse { $b=$this->findOwned($r,$id); return response()->json($b->load(['links','socialLinks','developmentKeys','note','websiteChecks'=>fn($q)=>$q->limit(10)])); }
 public function destroy(Request $r,string $id):JsonResponse { $b=$this->findOwned($r,$id); DB::transaction(function()use($b){foreach($b->developmentKeys()->get() as $key)DB::delete('delete from vault.secrets where id = ?',[$key->vault_secret_id]);$b->developmentKeys()->delete();$b->links()->delete();$b->socialLinks()->delete();$b->websiteChecks()->delete();$b->note()->delete();$b->delete();});return response()->json(null,204); }
}
