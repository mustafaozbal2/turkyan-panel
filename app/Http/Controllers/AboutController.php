<?php

namespace App\Http\Controllers;

use App\Models\ProjectValue;
use App\Models\TeamMember;
use Illuminate\Http\Request;

class AboutController extends Controller
{
    public function index()
    {
        $teamMembers = TeamMember::all();
        $projectValues = ProjectValue::all();

        return view('hakkimizda', [
            'team_members' => $teamMembers,
            'values' => $projectValues,
        ]);
    }
}