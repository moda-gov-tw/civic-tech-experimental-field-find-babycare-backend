<?php

namespace App\Http\Controllers\V1;

use App\Enums\DayCareMemberRole;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\DayCareMemberResource;
use App\Models\DayCare;
use App\Models\DayCareMember;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

class DayCareMemberController extends Controller
{
    public function index(DayCare $dayCare)
    {
        $this->authorize('viewAny', [DayCareMember::class, $dayCare]);

        return DayCareMemberResource::collection($dayCare->members()->paginate());
    }

    public function store(Request $request, DayCare $dayCare)
    {
        $this->authorize('create', [DayCareMember::class, $dayCare]);

        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
            'role' => ['required', new Enum(DayCareMemberRole::class)]
        ]);

        $user = User::select('id')->where('email', $validated['email'])->first();

        $dayCare->members()->attach($user, [
            'role' => $validated['role']
        ]);

        $member = $dayCare->members()->where('id', $user->id)->first();

        return (new DayCareMemberResource($member))
            ->response()
            ->setStatusCode(JsonResponse::HTTP_CREATED);
    }

    public function show(DayCare $dayCare, User $member)
    {
        $this->authorize('view', [DayCareMember::class, $dayCare]);

        return new DayCareMemberResource($member);
    }

    public function update(Request $request, DayCare $dayCare, User $member)
    {
        $this->authorize('update', [DayCareMember::class, $dayCare]);

        $validated = $request->validate([
            'role' => ['sometimes', new Enum(DayCareMemberRole::class)]
        ]);

        $dayCare->members()->updateExistingPivot($member, $validated);

        return new DayCareMemberResource($dayCare->members()->where('id', $member->id)->first());
    }

    public function destroy(DayCare $dayCare, User $member)
    {
        $this->authorize('delete', [DayCareMember::class, $dayCare]);

        $member->pivot->delete();

        return response()->noContent();
    }
}
