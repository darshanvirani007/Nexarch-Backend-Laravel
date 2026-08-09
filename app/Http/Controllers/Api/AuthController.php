<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller; use Illuminate\Http\Client\Response; use Illuminate\Http\JsonResponse; use Illuminate\Http\Request; use Illuminate\Support\Facades\Http;
class AuthController extends Controller {
 private function client(){return Http::acceptJson()->withHeaders(['apikey'=>config('services.supabase.anon_key')])->timeout(15);}
 private function relay(Response $x):JsonResponse{return response()->json($x->json() ?: ['message'=>'Authentication request failed.'],$x->status());}
 public function login(Request $r):JsonResponse{$d=$r->validate(['email'=>['required','email'],'password'=>['required','string']]);return $this->relay($this->client()->post(rtrim(config('services.supabase.url'),'/').'/auth/v1/token?grant_type=password',$d));}
 public function register(Request $r):JsonResponse{$d=$r->validate(['email'=>['required','email'],'password'=>['required','string','min:8'],'full_name'=>['nullable','string','max:160']]);return $this->relay($this->client()->post(rtrim(config('services.supabase.url'),'/').'/auth/v1/signup',['email'=>$d['email'],'password'=>$d['password'],'data'=>['full_name'=>$d['full_name']??null]]));}
 public function refresh(Request $r):JsonResponse{$d=$r->validate(['refresh_token'=>['required','string']]);return $this->relay($this->client()->post(rtrim(config('services.supabase.url'),'/').'/auth/v1/token?grant_type=refresh_token',$d));}
 public function forgotPassword(Request $r):JsonResponse{$d=$r->validate(['email'=>['required','email']]);$d['redirect_to']=rtrim(config('app.frontend_url'),'/').'/reset-password';return $this->relay($this->client()->post(rtrim(config('services.supabase.url'),'/').'/auth/v1/recover',$d));}
 public function logout(Request $r):JsonResponse{$x=$this->client()->withToken($r->bearerToken())->post(rtrim(config('services.supabase.url'),'/').'/auth/v1/logout');return $x->successful()?response()->json(null,204):$this->relay($x);}
 public function updatePassword(Request $r):JsonResponse{$d=$r->validate(['password'=>['required','string','min:8','confirmed']]);return $this->relay($this->client()->withToken($r->bearerToken())->put(rtrim(config('services.supabase.url'),'/').'/auth/v1/user',['password'=>$d['password']]));}
 public function me(Request $r):JsonResponse{return response()->json($r->attributes->get('supabase_user'));}
}
