<?php
namespace App\Http\Controllers\Api;
use App\Models\DailyTask; use App\Models\OwnedModel; use Illuminate\Database\Eloquent\Builder; use Illuminate\Http\Request;
class DailyTaskController extends OwnedCrudController { protected string $model=DailyTask::class; protected function rules(Request $r,?OwnedModel $x=null):array{return ['task'=>['required','string','max:500'],'task_date'=>['required','date'],'is_completed'=>['sometimes','boolean'],'display_order'=>['sometimes','integer','min:0']];} protected function applyFilters(Builder $q,Request $r):Builder { parent::applyFilters($q,$r); if($r->filled('date'))$q->whereDate('task_date',$r->date('date')); return $q;}}
