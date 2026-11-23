<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Http\Requests\Site\MemberDirectoryRequest;
use App\Models\Member;
use App\Services\MemberDirectoryService;
use Illuminate\View\View;

class MemberController extends Controller
{
    public function __construct(private MemberDirectoryService $directory)
    {
    }

    public function index(MemberDirectoryRequest $request): View
    {
        $filters = $request->validated();

        return view('public.anggota.index', [
            'members' => $this->directory->paginate($filters),
            'filters' => $filters,
            'options' => $this->directory->filterOptions(),
        ]);
    }

    public function show(Member $member): View
    {
        // Anggota yang dihapus (soft delete) sudah 404 dari route model binding.
        abort_unless($member->status_aktif, 404);

        return view('public.anggota.show', $this->directory->profile($member));
    }
}
