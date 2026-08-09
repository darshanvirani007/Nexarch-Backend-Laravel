<?php
namespace App\Http\Controllers\Api;
use App\Models\MyLink; use App\Models\OwnedModel; use Illuminate\Http\Request;
class MyLinkController extends OwnedCrudController { protected string $model=MyLink::class; protected function rules(Request $r,?OwnedModel $x=null):array{return ['link_type'=>['required','string','max:50'],'category'=>['required','string','max:50'],'name'=>['required','string','max:120'],'url'=>['required','url:http,https','max:2048'],'display_order'=>['sometimes','integer','min:0'],'is_active'=>['sometimes','boolean']];}}
