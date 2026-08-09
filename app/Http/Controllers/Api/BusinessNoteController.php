<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller; use App\Models\Business; use Illuminate\Http\JsonResponse; use Illuminate\Http\Request;
class BusinessNoteController extends Controller { public function upsert(Request $r,string $business):JsonResponse{$b=Business::ownedBy($r->attributes->get('user_id'))->findOrFail($business);$data=$r->validate(['content'=>['nullable','string','max:50000']]);$x=$b->note()->updateOrCreate(['business_id'=>$b->id],$data+['user_id'=>$r->attributes->get('user_id')]);return response()->json($x);}}
