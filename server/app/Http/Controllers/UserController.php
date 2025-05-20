<?php

namespace App\Http\Controllers;

use App\Services\HrService;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Http\Requests\SearchEmployeesByNameRequest;

class UserController extends Controller
{
    use ResponseTrait;
    protected $hrService;

    public function __construct(HrService $hrService)
    {
        $this->hrService = $hrService;
    }

    public function getUser(Request $request)
    {
        return $this->successResponse($request->user());
    }

    public function getEmployeesByCompany()
    {
        $users = $this->hrService->getAllEmployees();
        return response()->json([
            'success' => true,
            'payload' => $users,
            'data' => $users
        ]);
    }

    public function searchEmployeesByName(SearchEmployeesByNameRequest $request)
    {
        $name = $request->input('name');
        $users = $this->hrService->searchEmployeesByName($name);
        return response()->json([
            'success' => true,
            'payload' => $users,
            'data' => $users
        ]);
    }


    public function getEmployeeById(Request $request, $id)
    {
        if (!is_numeric($id) || intval($id) != $id) return $this->errorResponse('Invalid employee ID', 404);
        $user = $this->hrService->getEmployeeById((int)$id);
        if (!$user) return $this->errorResponse('Employee not found or not in your company', 404);
        return $this->successResponse($user);
    }
}
