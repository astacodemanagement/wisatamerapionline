<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class TutorialController extends Controller
{
    protected $filePath;

    public function __construct()
    {
       
        $this->filePath = storage_path('app/tutorial_setting.json');
    }
 
    public function getTutorialStatus()
    {
        
        if (File::exists($this->filePath)) {
            $data = json_decode(File::get($this->filePath), true);
            return response()->json($data);
        }
 
        return response()->json(['tutorialClosed' => false]);
    }
 
    public function setTutorialStatus(Request $request)
    {
       
        $status = $request->input('tutorialClosed');
        $data = ['tutorialClosed' => $status];
 
        File::put($this->filePath, json_encode($data, JSON_PRETTY_PRINT));

        return response()->json(['message' => 'Status tutorial updated']);
    }
}
