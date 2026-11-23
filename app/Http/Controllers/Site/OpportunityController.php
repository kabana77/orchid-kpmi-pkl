<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Http\Requests\Site\OpportunityDirectoryRequest;
use App\Models\Opportunity;
use App\Services\OpportunityBoardService;
use Illuminate\View\View;

class OpportunityController extends Controller
{
    public function __construct(private OpportunityBoardService $board)
    {
    }

    public function index(OpportunityDirectoryRequest $request): View
    {
        $filters = $request->validated();

        return view('public.peluang.index', [
            'opportunities' => $this->board->paginate($filters),
            'filters' => $filters,
        ]);
    }

    public function show(Opportunity $opportunity): View
    {
        return view('public.peluang.show', $this->board->detail($opportunity));
    }
}
