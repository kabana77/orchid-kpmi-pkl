<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Http\Requests\Site\BusinessDirectoryRequest;
use App\Models\Business;
use App\Services\BusinessDirectoryService;
use Illuminate\View\View;

class BusinessController extends Controller
{
    public function __construct(private BusinessDirectoryService $directory)
    {
    }

    public function index(BusinessDirectoryRequest $request): View
    {
        $filters = $request->validated();

        return view('public.usaha.index', [
            'businesses' => $this->directory->paginate($filters),
            'filters' => $filters,
            'options' => $this->directory->filterOptions(),
        ]);
    }

    public function show(Business $business): View
    {
        // Usaha yang dihapus sudah 404 dari route model binding.
        // Usaha milik anggota yang nonaktif atau terhapus juga disembunyikan.
        $business->loadMissing('member');
        abort_unless($business->member?->status_aktif, 404);

        return view('public.usaha.show', $this->directory->profile($business));
    }
}
