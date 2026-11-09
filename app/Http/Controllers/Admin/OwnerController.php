<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\OwnerRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\OwnerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class OwnerController extends Controller
{
    public function __construct(private OwnerService $owners)
    {
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        return UserResource::collection(
            $this->owners->paginate((string) $request->query('search', ''))
        );
    }

    public function store(OwnerRequest $request): JsonResponse
    {
        $owner = $this->owners->create($request->validated());

        return (new UserResource($owner))->response()->setStatusCode(201);
    }

    public function show(User $owner): UserResource
    {
        $this->ensureOwner($owner);

        return new UserResource($owner->load('shop:id,owner_id,name,slug'));
    }

    public function update(OwnerRequest $request, User $owner): UserResource
    {
        $this->ensureOwner($owner);

        return new UserResource($this->owners->update($owner, $request->validated()));
    }

    public function destroy(User $owner): Response
    {
        $this->ensureOwner($owner);
        $this->owners->delete($owner);

        return response()->noContent();
    }

    private function ensureOwner(User $user): void
    {
        abort_unless($user->role === User::ROLE_OWNER, 404);
    }
}
