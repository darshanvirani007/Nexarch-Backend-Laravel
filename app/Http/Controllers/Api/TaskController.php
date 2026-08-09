<?php
namespace App\Http\Controllers\Api;
use App\Models\Task; use App\Models\OwnedModel; use Illuminate\Http\Request;
class TaskController extends OwnedCrudController { protected string $model=Task::class; protected function rules(Request $r,?OwnedModel $x=null):array{return ['task'=>['required','string','max:500'],'is_completed'=>['sometimes','boolean'],'display_order'=>['sometimes','integer','min:0']];}}
