<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\AdministrativeGroupResource;
use App\Models\AdministrativeGroup;
use Illuminate\Http\Request;

class AdministrativeGroupController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', AdministrativeGroup::class);

        return AdministrativeGroupResource::collection(
            $request->user()->isSuperUser()
                ? AdministrativeGroup::paginate()
                : $request->user()->administrativeGroups()->paginate()
        );
    }

    public function store(Request $request)
    {
        $this->authorize('create', AdministrativeGroup::class);

        $validated = $request->validate([
            'name' => 'required|string'
        ]);

        return new AdministrativeGroupResource(AdministrativeGroup::create($validated));
    }

    public function show(AdministrativeGroup $group)
    {
        $this->authorize('view', $group);

        return new AdministrativeGroupResource($group);
    }

    public function update(Request $request, AdministrativeGroup $group)
    {
        $this->authorize('update', $group);

        $validated = $request->validate([
            'name' => 'sometimes|string'
        ]);

        $group->update($validated);
    }

    public function destroy(AdministrativeGroup $group)
    {
        $this->authorize('delete', $group);

        $group->delete();
    }
}
