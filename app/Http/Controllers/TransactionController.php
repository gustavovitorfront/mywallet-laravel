<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{

    public function form_add()
    {
        return view('form_transaction', [
            'data' => [],
            'type' => 'add'
        ]);
    }

    public function form_edit($id)
    {
        try {
            $transaction = Transaction::findOrFail($id);

            return view('form_transaction', [
                'data' => $transaction,
                'type' => 'edit'
            ]);
        } catch (\Throwable $th) {
            return redirect()->route('home')->with('error', 'Transação não encontrada.');
        }
    }

    public function form_add_submit(Request $request)
    {
        $request->validate([
            'description' => 'required|string|max:150',
            'value' => 'required',
            'category' => 'required|string|max:150'
        ]);

        $priceString = str_replace(".", "", $request->post('value'));
        $priceFloat = str_replace(",", ".", $priceString);
        $priceDouble = (float) $priceFloat;

        try {
            $stored = DB::statement('CALL insert_transaction(?, ?, ?, ?, ?, ?, ?, ?)', [
                auth()->user()->id,
                $request->post('description'),
                $priceDouble,
                $request->post('category'),
                $request->post('payed') == 'on' ? 1 : 0,
                $request->post('expend') == 'on' ? 1 : 0,
                date('Y-m-d h:i:s'),
                date('Y-m-d h:i:s'),
            ]);

            if ($stored)
                return redirect()->route('home')->with('success', 'Transação inserida com sucesso!');
            else
                return view('form_transaction', [
                    'data' => [],
                    'type' => 'add',
                    'error' => 'Uma falha ocorreu tente novamente mais tarde.',
                    'request' => $request
                ]);
        } catch (\Throwable $th) {
            return view('form_transaction', [
                'data' => [],
                'type' => 'add',
                'error' => 'Uma falha ocorreu tente novamente mais tarde.',
                'request' => $request
            ]);
        }
    }

    public function form_edit_submit($id, Request $request)
    {
        $transaction = Transaction::find($id);
        if(empty($transaction)){
            return redirect()->route('home')->with('error', 'Transação não encontrada.');
        }

        $request->validate([
            'description' => 'required|string|max:150',
            'value' => 'required',
            'category' => 'required|string|max:150'
        ]);

        $priceString = str_replace(".", "", $request->post('value'));
        $priceFloat = str_replace(",", ".", $priceString);
        $priceDouble = (float) $priceFloat;

        try {
            $stored = DB::statement('CALL update_transaction(?, ?, ?, ?, ?, ?, ?, ?, ?)', [
                $transaction->id,
                auth()->user()->id,
                $request->post('description'),
                $priceDouble,
                $request->post('category'),
                $request->post('payed') == 'on' ? 1 : 0,
                $request->post('expend') == 'on' ? 1 : 0,
                $transaction->created_at,
                date('Y-m-d h:i:s'),
            ]);

            if ($stored)
                return redirect()->route('home')->with('success', 'Transação atualizada com sucesso!');
            else
                return view('form_transaction', [
                    'data' => [],
                    'type' => 'add',
                    'error' => 'Uma falha ocorreu tente novamente mais tarde.'
                ]);
        } catch (\Throwable $th) {
            return view('form_transaction', [
                'data' => [],
                'type' => 'add',
                'error' => 'Uma falha ocorreu tente novamente mais tarde.'
            ]);
        }
    }

    public function delete($id)
    {
        $transaction = Transaction::find($id);
        if(empty($transaction)){
            return redirect()->route('home')->with('error', 'Transação não encontrada.');
        }

        if($transaction->delete()){
            return redirect()->route('home')->with('success', 'Transação deletada com sucesso!');
        }else{
            return redirect()->route('home')->with('error', 'Tente novamente mais tarde.');
        }
    }
}
