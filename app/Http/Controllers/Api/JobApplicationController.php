<?php
namespace App\Http\Controllers\Api;
use App\Models\JobApplication; use App\Models\OwnedModel; use Illuminate\Http\Request;
class JobApplicationController extends OwnedCrudController { protected string $model=JobApplication::class; protected function rules(Request $r,?OwnedModel $x=null):array{return ['job_name'=>['required','string','max:200'],'job_link'=>['nullable','url:http,https','max:2048'],'status'=>['required','in:interested,applied,interview,offer,rejected,withdrawn'],'display_order'=>['sometimes','integer','min:0']];}}
