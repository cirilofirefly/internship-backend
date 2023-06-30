<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Services\ChangePasswordService;

class ChangePasswordController extends Controller
{
    protected $changePasswordService;

    public function __construct(ChangePasswordService $changePasswordService)
    {
        $this->changePasswordService = $changePasswordService;
    }
    public function changePassword(ChangePasswordRequest $request)
    {
        [$status_code, $message] = $this->changePasswordService->changePassword($request->validated());
        return response()->json(['message' => $message], $status_code);
    }
}
