@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row mb-2">
            <div class="col-md-12">

                @if (\Session::has('success'))
                    <div class="alert alert-success" role="alert">
                        {!! \Session::get('success') !!}
                    </div>
                @else
                    <div class="alert alert-success" role="alert">
                        Olá <strong>{{ auth()->user()->name }} {{ auth()->user()->last_name }}</strong>, seja bem vindo a
                        sua
                        carteira.
                    </div>
                @endif

                @if (\Session::has('error'))
                    <div class="alert alert-danger">
                        <ul style="margin: 0px;">
                            <li>{!! \Session::get('error') !!}</li>
                        </ul>
                    </div>
                @endif

            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h3>Saldo</h3>
                        <h1>R$ {{ number_format(auth()->user()->balance, 2, ',', '.') }}</h1>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-danger">
                    <div class="card-body text-danger">
                        <h3>Total de despesas</h3>
                        <h1>R$ {{ number_format($totalExpenses, 2, ',', '.') }}</h1>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-success">
                    <div class="card-body text-success">
                        <h3>Total de receitas</h3>
                        <h1>R$ {{ number_format($totalRevenue, 2, ',', '.') }}</h1>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        Transações
                    </div>
                    <div class="card-body">
                        <div class="row justify-content-between align-items-center mb-4">
                            <div class="col-md-4">
                                <form method="GET" action="{{ route('home') }}">
                                    <div class="form-group">
                                        <label for="periodo"><b>Filtrar por periodo</b></label>
                                        <input type="month" name="periodo" class="form-control" id="periodo"
                                            value="{{ !empty($periodo) ? $periodo : date('Y-m') }}">
                                    </div>
                                </form>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group" style="float: right">
                                    <a href="{{ route('add_transaction') }}" class="btn btn-primary">Cadastrar uma nova
                                        transação</a>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-transactions table-striped">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Descrição</th>
                                        <th scope="col">Categoria</th>
                                        <th scope="col">Valor</th>
                                        <th scope="col">Paga?</th>
                                        <th scope="col">Tipo</th>
                                        <th scope="col">Cadastrado em</th>
                                        <th scope="col">Atualizado em</th>
                                        <th scope="col">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($transactions as $transaction)
                                        <tr>
                                            <th scope="row">{{ $transaction->id }}</th>
                                            <td>{{ $transaction->description }}</td>
                                            <td>{{ $transaction->category }}</td>
                                            <td>R$ {{ number_format($transaction->value, 2, ',', '.') }}</td>
                                            <td>{{ $transaction->payed ? 'Pago' : 'Não pago' }}</td>
                                            <td class="{{ $transaction->expend ? 'text-danger' : 'text-success' }}">
                                                {{ $transaction->expend ? 'Despesa' : 'Receita' }}</td>
                                            <td>{{ date('d/m/Y h:i:s', strtotime($transaction->created_at)) }}</td>
                                            <td>{{ date('d/m/Y h:i:s', strtotime($transaction->updated_at)) }}</td>
                                            <td>
                                                <a href="{{ route('form_edit_submit', $transaction->id) }}"
                                                    style="text-decoration: none">
                                                    <i class="fa fa-pen"></i>
                                                </a>

                                                <a href="#" data-id="{{ $transaction->id }}" class="removeTransaction" style="text-decoration: none">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('.table-transactions').DataTable();

            $('#periodo').change(function() {
                $(this).parent('.form-group').parent('form').submit();
            })

            $('.removeTransaction').click(function(){
                var id = $(this).attr('data-id');
                var url = '{{config("app.url")}}'+'/'+'delete-transaction/'+id

                if (confirm("Deseja realmente excluir essa transação?") == true) {
                    window.location.href = url;
                }
            })
        });
    </script>
@endsection
