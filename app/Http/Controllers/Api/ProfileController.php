<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller; use App\Models\Profile; use Illuminate\Http\JsonResponse; use Illuminate\Http\Request; use Illuminate\Support\Arr;
class ProfileController extends Controller {
 public function show(Request $r):JsonResponse{$id=$r->attributes->get('user_id');$p=Profile::find($id);return response()->json(['profile'=>$p,'email'=>data_get($r->attributes->get('supabase_user'),'email')]);}
 public function update(Request $r):JsonResponse{$d=$r->validate(['full_name'=>['nullable','string','max:160'],'country'=>['nullable','string','max:100'],'contact_no'=>['nullable','string','max:40'],'timezone'=>['required','timezone:all']]);$p=Profile::updateOrCreate(['id'=>$r->attributes->get('user_id')],$d);return response()->json($p);}
}
