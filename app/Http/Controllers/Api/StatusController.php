<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Server;
use Illuminate\Http\Request;

class StatusController extends Controller
{
    private $rules = [
        'total_cpu' => 'required|numeric',
        'used_cpu' => 'required|numeric',

        'total_memory' => 'required|numeric',
        'used_memory' => 'required|numeric',

        'disks' => 'required|array',
        'disks.*.name' => 'required|string',
        'disks.*.total_space' => 'required|numeric',
        'disks.*.used_space' => 'required|numeric',
    ];
    public function post(Server $server, Request $request)
    {

        $data = $request->validate($this->rules);
        $server->stats()->create($data);
    }
}
