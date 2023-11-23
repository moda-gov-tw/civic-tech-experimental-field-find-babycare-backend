<?php

namespace App\Http\Controllers\V1;

use App\Enums\AdministrativeGroupMemberRole;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\AdministrativeGroupMemberResource;
use App\Models\AdministrativeGroup;
use App\Models\AdministrativeGroupMember;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

class AdministrativeGroupMemberController extends Controller
{
    public function index(AdministrativeGroup $group)
    {
        $this->authorize('viewAny', [AdministrativeGroupMember::class, $group]);

        return AdministrativeGroupMemberResource::collection($group->members()->paginate());
    }

    public function store(Request $request, AdministrativeGroup $group)
    {
        $this->authorize('create', [AdministrativeGroupMember::class, $group]);

        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
            'role' => ['required', new Enum(AdministrativeGroupMemberRole::class)]
        ]);

        $user = User::select('id')
            ->where('email', $validated['email'])
            ->first();

        $group->members()->attach($user, [
            'role' => $validated['role']
        ]);

        $member = $group->members()->where('id', $user->id)->first();

        return (new AdministrativeGroupMemberResource($member))
            ->response()
            ->setStatusCode(JsonResponse::HTTP_CREATED);
    }

    public function show(AdministrativeGroup $group, User $member)
    {
        $this->authorize('view', [AdministrativeGroupMember::class, $group]);

        return new AdministrativeGroupMemberResource($member);
    }

    public function update(Request $request, AdministrativeGroup $group, User $member)
    {
        $this->authorize('update', [AdministrativeGroupMember::class, $group]);

        $validated = $request->validate([
            'role' => ['sometimes', new Enum(AdministrativeGroupMemberRole::class)]
        ]);

        $group->members()->updateExistingPivot($member, $validated);
    }

    public function destroy(AdministrativeGroup $group, User $member)
    {
        $this->authorize('delete', [AdministrativeGroupMember::class, $group]);

        $member->pivot->delete();
    }
}
