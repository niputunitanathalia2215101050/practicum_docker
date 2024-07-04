<?php

namespace App\Http\Controllers;

use App\Models\Confirmation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class confirmationController extends Controller
{
    public function get_confirmation(){
        try {
         $confirmation = confirmation::with('user')->get();
         if ($confirmation){
             return response()->json([
                 'status' => 200,
                 'data' => $confirmation
             ], 200);
         }
         else
         {
             return response()->json([
                 'status' => 404,
                 'message' => 'Data not found',
             ], 404);
         }
        } catch (\Throwable $th) {
         return response()->json([
             'status' => 500,
             'message' => $th->getMessage(),
         ], 500);
        }
     }
     
     public function create_confirmation(Request $request)
     {
         try {
             $validasi = Validator::make($request->all(), [
                 'nim' => 'required',
                 'bukti_pembayaran' => 'image',
             ]);
             if ($validasi->fails()) {
                 return response()->json([
                     'status' => 400,
                     'message' => $validasi->errors()
                 ], 400);
             }
 
             $image = $request->file('bukti_pembayaran');
             $imageName = time() . '.' . $image->getClientOriginalExtension();
             $image->storeAs('public/confirmation_payment', $imageName);
 
             $confirmation = Confirmation::create([
                 'nim' => $request->nim,
                 'bukti_pembayaran' => $imageName,
             ]);
 
             return response()->json([
                 'status' => 201,
                 'message' => 'Confirmation created successfully',
                 'data' => $confirmation
             ], 201);
         } catch (\Throwable $th) {
             return response()->json([
                 'status' => 500,
                 'message' => $th->getMessage()
             ], 500);
         }
     }
 
     public function update_confirmation(Request $request, Confirmation $confirmation)
     {
         try {
             if (!$confirmation) {
                 return response()->json([
                     'status' => 404,
                     'message' => 'News not found',
                 ], 404);
             }
 
             $validasi = Validator::make($request->all(), [
                 'nim' => 'string',
                 'bukti_pembayaran' => 'image',
             ]);
 
             if ($validasi->fails()) {
                 return response()->json([
                     'status' => 400,
                     'message' => 'Validation failed',
                     'errors' => $validasi->errors()
                 ], 400);
             }
 
             if ($request->hasFile('bukti_pembayaran')) {
                 if ($confirmation->image) {
                     Storage::delete('public/confirmation_payment' . $confirmation->image);
                 }
                 $image = $request->file('bukti_pembayaran');
                 $imageName = time() . '.' . $image->getClientOriginalExtension();
                 $image->storeAs('public/confirmation_paymemnt', $imageName);
 
                 $confirmation->update([
                     'nim' => $request->nim,
                     'bukti_pembayaran' => $imageName,
                 ]);
             } else {
                 $confirmation->update([
                     'nim' => $request->nim,
                     'bukti_pembayaran' => $request->bukti_pembayaran,
                 ]);
             }
 
             return response()->json([
                 'status' => 201,
                 'message' => 'Confirmation update successfully',
                 'data' => $confirmation
             ], 201);
         } catch (\Throwable $th) {
             return response()->json([
                 'status' => 500,
                 'message' => $th->getMessage()
             ], 500);
         }
     }

     
  
     public function delete_confirmation(confirmation $confirmation){
         try {
             if (!$confirmation) {
                 return response()->json([
                     'status' => 404,
                     'message' => 'confirmation not found!',
                 ], 404);
             }
             $confirmation->delete();
             return response()->json([
                 'status' => 200,
                 'message' => "confirmation delete successfully!",
             ], 200);
         } catch (\Throwable $th) {
             return response()->json([
                 'status' => 500,
                 'message' => $th->getMessage(),
             ], 500);
         }
}
}