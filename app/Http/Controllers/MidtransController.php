<?php

namespace App\Http\Controllers;

use App\Mail\TransactionSuccess;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Midtrans\Config;
use Midtrans\Notification;

class MidtransController extends Controller
{
    public function notificationHandler(Request $request)
    {
        //Set konfigurasi midtrans
        Config::$serverKey = config('midtrans.serverKey');
        Config::$isProduction = config('midtrans.isProduction');
        Config::$isSanitized = config('midtrans.isSanitized');
        Config::$is3ds = config('midtrans.is3ds');

        //Buat Instance midtrans notification
        $notification = new Notification();

        //Pecah order id agar bisa diterima oleh database
        $order = explode("-", $notification->order_id);

        //Asign ke variable untuk memudahkan koding
        $status = $notification->transaction_status;
        $type = $notification->payment_type;
        $fraud = $notification->fraud_status;
        $order_id = $order[1];
        
        //Cari transaksi berdasarkan id
        $transaction = Transaction::findOrFail($order_id);

        //Handle Notification status midtrans
        if ($status = 'capture') {
            if ($type = 'credit_card') {
                if ($fraud = 'challange') {
                    $transaction->transaction_status == "CHALLANGE";
                } else {
                    $transaction->transaction_status == "SUCCESS";
                }
            }
        } elseif ($status = 'settlement') {
            $transaction->transaction_status == "SUCCESS";
        } elseif ($status = 'pending') {
            $transaction->transaction_status == "PENDING";
        } elseif ($status = 'deny') {
            $transaction->transaction_status == "FAILED";
        } elseif ($status = 'expire') {
            $transaction->transaction_status == "EXPIRED";
        } elseif ($status = 'cancel') {
            $transaction->transaction_status == "FAILED";
        }

        //simpan transaksi
        $transaction->save();

        //kirim email
        if ($transaction) {
            if ($status = 'capture' && $fraud = 'accept') {
                Mail::to($transaction->user)->send(
                    new TransactionSuccess($transaction)
                );
            } elseif ($status = 'settlement') {
                Mail::to($transaction->user)->send(
                    new TransactionSuccess($transaction)
                );
            } elseif ($status = 'success') {
                Mail::to($transaction->user)->send(
                    new TransactionSuccess($transaction)
                );
            } elseif ($status = 'capture' && $fraud = 'challange') {
                return response()->json([
                    'meta' => [
                        'code' => 200,
                        'massage' => 'Midtrans Payment Challange'
                    ]
                ]);
            } else {
                return response()->json([
                    'meta' => [
                        'code' => 200,
                        'massage' => 'Midtrans Payment not settlement'
                    ]
                ]);
            }

            return response()->json([
                'meta' => [
                    'code' => 200,
                    'massage' => 'Midtrans notification success'
                ]
            ]);
        }
    }
    public function finishRedirect(Request $request)
    {
        return view('pages.success');
    }
    public function unfinishRedirect(Request $request)
    {
        return view('pages.unfinish');
    }
    public function errorRedirect(Request $request)
    {
        return view('pages.failed');
    }
}
