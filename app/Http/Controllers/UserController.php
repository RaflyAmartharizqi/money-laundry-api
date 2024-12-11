<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Helpers\ApiResponse;
use App\Constants\Messages;

class UserController extends Controller
{
    public function index()
    {
        try 
        {
            $user = User::with('account_status')->get();
            $totalData = $user->count();

            $data = [
                'total_data' => $totalData,
                'users' => $user
            ];

            return ApiResponse::success(Messages::SUCCESS_GET_DATA, 200, $data);
        } catch (\Exception $e) {
            return ApiResponse::error(Messages::ERROR_GET_DATA, 404, $e->getMessage());
        }
    }

    public function destroy ($userId)
    {
        try 
        {
            $user = User::find($userId);
            if ($user == null) {
                return ApiResponse::error(Messages::ERROR_GET_DATA, 404);
            }
            $user->delete();
            return ApiResponse::success(Messages::SUCCESS_DELETE_DATA, 200, $user);
        } catch (\Exception $e) {
            return ApiResponse::error(Messages::ERROR_DELETE_DATA, 404, $e->getMessage());
        }
    }
}
