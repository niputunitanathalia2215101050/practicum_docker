<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    public function get_payment(){
       try {
        $payment = Payment::with('user')->get();
        if ($payment){
            return response()->json([
                'status' => 200,
                'data' => $payment
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

    public function create_payment(Request $request){
        try {
            $validasi = Validator::make($request->all(), [
                'nim' => 'required|string',
                'periode' => 'required|string',
                'semester' => 'required|string',
                'total_pembayaran' => 'required|string',
            ]);
            if ($validasi->fails()) {
                return response()->json([
                    'status' => 400,
                    'message' =>$validasi->errors(),
                ], 400);
            } 

            $payment = Payment::create([
                'nim' => $request->nim,
                'periode' => $request->periode,
                'semester' => $request->semester,
                'total_pembayaran' => $request->total_pembayaran,
            ]);
            return response()->json([
                'status' => 201,
                'message' => 'Create payment success',
                'data' => $payment,
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 500,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function show_payment(Request $request, Payment $payment){
        try {
            $show = Payment::findOrFails($payment)->first();
            if (!$show){
                return response()->json([
                    'status' => 404,
                    'message' => 'Payment not found!',
                ], 404);
            } else {
                return response()->json([
                    'status' => 200,
                    'data' => $show, 
                ], 200);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    public function update_payment(Request $request, Payment $payment){
        try {
            $update = Payment::where('id', $payment->id)->first();
            if ($update) {
                $validasi = Validator::make($request->all(), [
                    'nim' => 'required|string',
                    'periode' => 'required|string',
                    'semester' => 'required|string',
                    'total_pembayaran' => 'required|string',
                ]);
                if ($validasi->fails()) {
                    return response()->json([
                        'status' => 400,
                        'message' =>$validasi->errors(),
                    ], 400);
                } 
                $update->update([
                    'nim' => $request->nim,
                    'periode' => $request->periode,
                    'semester' => $request->semester,
                    'total_pembayaran' => $request->total_pembayaran,
                ]);
                return response()->json([
                    'status' => 200,
                    'message' => 'Payment success to update!',
                    'data' => $update
                ], 200);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => 'Payment not found',
                ], 404);
            }
            
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 500,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function delete_payment(Payment $payment){
        try {
            if (!$payment) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Payment not found!',
                ], 404);
            }
            $payment->delete();
            return response()->json([
                'status' => 200,
                'message' => "Payment delete successfully!",
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 500,
                'message' => $th->getMessage(),
            ], 500);
        }
    }
}
