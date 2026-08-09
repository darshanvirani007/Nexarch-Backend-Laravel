<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OwnedModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

abstract class OwnedCrudController extends Controller
{
    /** @var class-string<OwnedModel> */
    protected string $model;
    abstract protected function rules(Request $request, ?OwnedModel $record = null): array;

    protected function query(Request $request): Builder
    {
        return $this->model::query()->ownedBy($request->attributes->get('user_id'));
    }

    protected function applyFilters(Builder $query, Request $request): Builder
    {
        if ($request->filled('status')) $query->where('status', $request->string('status'));
        if ($request->has('is_completed')) $query->where('is_completed', $request->boolean('is_completed'));
        if ($request->filled('category')) $query->where('category', $request->string('category'));
        return $query;
    }

    public function index(Request $request): JsonResponse
    {
        $query = $this->applyFilters($this->query($request), $request);
        $order = $request->string('sort', 'display_order')->toString();
        if (! in_array($order, ['display_order','created_at','updated_at','deadline','task_date'], true)) $order='display_order';
        return response()->json($query->orderBy($order)->orderBy('created_at')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate($this->rules($request));
        $record = $this->model::create($data + ['user_id'=>$request->attributes->get('user_id')]);
        return response()->json($record, 201);
    }

    public function show(Request $request, string $id): JsonResponse
    { return response()->json($this->findOwned($request, $id)); }

    public function update(Request $request, string $id): JsonResponse
    {
        $record=$this->findOwned($request,$id);
        $rules = collect($this->rules($request,$record))->map(function ($rule) {
            $rules = is_array($rule) ? $rule : explode('|', $rule);
            return in_array('required', $rules, true) ? array_merge(['sometimes'], $rules) : $rules;
        })->all();
        $record->update($request->validate($rules));
        return response()->json($record->fresh());
    }

    public function destroy(Request $request, string $id): JsonResponse
    { $this->findOwned($request,$id)->delete(); return response()->json(null,204); }

    protected function findOwned(Request $request, string $id): OwnedModel
    { return $this->query($request)->findOrFail($id); }
}
