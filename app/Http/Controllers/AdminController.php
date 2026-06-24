<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
     public function getAgents(Request $request){

     if ($request->user()->role !== 'admin')
    {
        return response()->json(['message'=> " Nincs jogosultságod ehhez a végponthoz"], 403);
    }

    $agents = User::where('role', 'agent')->orderBy('created_at', 'desc')->get();

    return response()->json($agents,200);

     }

     //ingatlanos törlése "admin"

     public function deleteAgent(Request $request){
        if($request->user()-role!=='admin'){
            return response()->json(['message'=> " Nincs jogosultságod"], 403);
        }
        $agent = User::where('role', 'agent')->findOrFail($id);
        $agent->delete();
        return response()->json(['message' => 'Ingatlanos sikeresen törölve a rendszerből.'], 200);
     }
}
