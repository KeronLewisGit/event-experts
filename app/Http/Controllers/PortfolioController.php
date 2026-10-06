<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\View\View;

class PortfolioController extends Controller
{
    public function index(): View
    {
        return view('portfolio.index', ['projects' => Project::published()->get()]);
    }

    public function show(Project $project): View
    {
        abort_unless($project->is_published || auth()->check(), 404);

        return view('portfolio.show', [
            'project' => $project,
            'more' => Project::published()->whereKeyNot($project->id)->take(3)->get(),
        ]);
    }
}
