<?php

namespace App\Http\Controllers;

use App\Models\Conf\KeyResourcePerson;
use App\Models\Conf\KeyTraining;
use App\Models\Conf\Learning;
use App\Models\Conf\OfficeRepresentative;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ConfigurationsController extends Controller
{
    public function Index(){
        return Inertia::render('Configuration/Index');
    }

    public function GetKeyTraining(){
        return Inertia::render('Configuration/Preview', [
            'link' => 'conf.training',
            'title' => 'Key Area: Training',
            'items' => KeyTraining::get()
        ]);
    }
    public function GetKeyResourcePerson(){
        return Inertia::render('Configuration/Preview', [
            'link' => 'conf.resource-person',
            'title' => 'Key Area: Resource Person',
            'items' => KeyResourcePerson::get()
        ]);
    }
    public function GetLearning(){
        return Inertia::render('Configuration/Preview', [
            'link' => 'conf.learning',
            'title' => 'Key Area: Learning',
            'items' => Learning::get()
        ]);
    }
    public function GetOfficeRepresentative(){
        return Inertia::render('Configuration/Preview', [
            'link' => 'conf.office-representative',
            'title' => 'Office Representative',
            'items' => OfficeRepresentative::get()
        ]);
    }
}
