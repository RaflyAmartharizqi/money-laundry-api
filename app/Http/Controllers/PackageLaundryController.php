<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\PackageLaundryModel;
use App\Helpers\ApiResponse;
use App\Constants\Messages;
use App\Models\TransactionMemberModel;
use Illuminate\Support\Facades\Auth;

class PackageLaundryController extends Controller
{
    public function index($usersId)
    {
        try 
        {
            $packageLaundry = PackageLaundryModel::where('users_id', $usersId)->get();
            return ApiResponse::success(Messages::SUCCESS_GET_DATA, 200, $packageLaundry);
        } catch (\Exception $e) {
            return ApiResponse::error(Messages::ERROR_GET_DATA, 404, $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try 
        {
            $validator = Validator::make($request->all(), [
                'users_id' => 'required|integer',
                'name' => 'required|string',
                'price_per_kg' => 'required|integer',
                'description' => 'required|string',
                'logo' => 'required|string',
            ]);

            if ($validator->fails()) {
                return ApiResponse::error(Messages::ERROR_VALIDATION, 400, $validator->errors());
            }

            $packageLaundry = PackageLaundryModel::create([
                'users_id' => $request->users_id,
                'name' => $request->name,
                'logo' => $request->logo,
                'price_per_kg' => $request->price_per_kg,
                'description' => $request->description,
            ]);

            return ApiResponse::success(Messages::SUCCESS_CREATE_DATA, 200, $packageLaundry);
        } catch (\Exception $e) {
            return ApiResponse::error(Messages::ERROR_CREATE_DATA, 404, $e->getMessage());
        }
    }

    public function update(Request $request, $packageLaundryId)
    {
        try 
        {
            $validator = Validator::make($request->all(), [
                'users_id' => 'required|integer',
                'name' => 'required|string',
                'logo' => 'required|string',
                'price_per_kg' => 'required|integer',
                'description' => 'required|string',
            ]);

            if ($validator->fails()) {
                return ApiResponse::error(Messages::ERROR_VALIDATION, 400, $validator->errors());
            }

            $packageLaundry = PackageLaundryModel::find($packageLaundryId);
            if (is_null($packageLaundry)) {
                return ApiResponse::error(Messages::ERROR_NOT_FOUND, 404, null);
            }

            $packageLaundry->update([
                'users_id' => $request->users_id,
                'name' => $request->name,
                'logo' => $request->logo,
                'price_per_kg' => $request->price_per_kg,
                'description' => $request->description,
            ]);

            return ApiResponse::success(Messages::SUCCESS_UPDATE_DATA, 200, $packageLaundry);
        } catch (\Exception $e) {
            return ApiResponse::error(Messages::ERROR_UPDATE_DATA, 404, $e->getMessage());
        }
    }

    public function destroy($packageLaundryId)
    {
        try 
        {
            $packageLaundry = PackageLaundryModel::find($packageLaundryId);
            if (is_null($packageLaundry)) {
                return ApiResponse::error(Messages::ERROR_NOT_FOUND, 404, null);
            }
            $packageLaundry->delete();
            return ApiResponse::success(Messages::SUCCESS_DELETE_DATA, 200, $packageLaundry);
        } catch (\Exception $e) {
            return ApiResponse::error(Messages::ERROR_DELETE_DATA, 404, $e->getMessage());
        }
    }
}
