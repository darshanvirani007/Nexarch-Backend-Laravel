<?php
namespace App\Http\Controllers\Api;
use App\Models\Goal; use App\Models\OwnedModel; use Illuminate\Http\Request;
class GoalController extends OwnedCrudController { protected string $model=Goal::class; protected function rules(Request $r,?OwnedModel $x=null):array{return ['title'=>['required','string','max:200'],'category'=>['required','string','max:50'],'measure'=>['required','string','max:120'],'deadline'=>['nullable','date'],'display_order'=>['sometimes','integer','min:0'],'current_value'=>['required','numeric'],'target_value'=>['required','numeric','gt:0'],'unit'=>['required','string','max:40']];}}
