<?php
namespace App\Http\Controllers\Api;
use App\Models\Learning; use App\Models\OwnedModel; use Illuminate\Http\Request;
class LearningController extends OwnedCrudController { protected string $model=Learning::class; protected function rules(Request $r,?OwnedModel $x=null):array{return ['title'=>['required','string','max:200'],'category'=>['required','string','max:50'],'status'=>['required','in:to_learn,not_started,in_progress,completed'],'provider_or_author'=>['nullable','string','max:160'],'resource_url'=>['nullable','url:http,https','max:2048'],'display_order'=>['sometimes','integer','min:0']];}}
