<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjectFormRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Workspace $workspace, ProjectFormRequest $request)
    {
        $validatedData = $request->validated();

        if($request->user()->id != $workspace->created_by_id){
            return Response()->json([
                "messages" => "L'utilisateur n'a pas accès à ce projet ou ne possède pas les droits nécessaires !"
            ], Response::HTTP_FORBIDDEN);
        }

        $projet = Project::create(
            $validatedData + [
                "workspace_id" => $workspace->id
            ]
        );

        return (new ProjectResource($projet))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
