@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row mb-2">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        {{ $type == 'add' ? 'Adicionar nova' : 'Editar' }} transação
                    </div>
                    <div class="card-body">
                        @if (!empty($errors->all()) || isset($error))
                            <div class="alert alert-danger">
                                <ul style="margin: 0px;">
                                    @if (isset($error))
                                        <li>{!! $error !!}</li>
                                    @endif

                                    @if (!empty($errors->all()))
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    @endif
                                </ul>
                            </div>
                        @endif

                        <form method="POST"
                            action="{{ $type == 'add' ? route('form_add_submit') : route('form_edit_submit', $data->id) }}">
                            @csrf

                            @if ($type == 'edit')
                                @method('PUT')
                            @endif

                            <div class="form-group mb-3">
                                <label for="input_description">Descrição</label>
                                <input type="text" name="description" class="form-control" id="input_description"
                                    value="{{ old('description') ?? ($data->description ?? '') }}">
                            </div>

                            @php
                                $value = old('value') ?? ($data->value ?? '');
                                $value = $value ? number_format($value, 2, ',', '.') : '';
                            @endphp
                            <div class="form-group mb-3">
                                <label for="input_value">Valor (R$)</label>
                                <input type="text" name="value" class="form-control" id="input_value"
                                    value="{{ $value }}">
                            </div>

                            <div class="form-group mb-3">
                                <label for="input_category">Categoria</label>
                                <input type="text" name="category" class="form-control" id="input_category"
                                    value="{{ old('category') ?? ($data->category ?? '') }}">
                            </div>

                            <div class="mb-3">
                                <div class="form-group form-check form-check-inline">
                                    <input type="checkbox" class="form-check-input" name="payed" id="payed"
                                        {{ $data->payed ?? '' || old('payed') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="payed">Pago</label>
                                </div>

                                <div class="form-group form-check form-check-inline">
                                    <input type="checkbox" class="form-check-input" name="expend" id="expend"
                                        {{ $data->expend ?? '' || old('expend') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="expend">Despesa</label>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">Enviar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/jquery.mask.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#input_value').mask('000.000.000.000.000,00', {
                reverse: true
            });
        });
    </script>
@endsection
