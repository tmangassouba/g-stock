@extends('layouts.auth')

@section('card-header-title', 'Réinitialiser le mot de passe')

@section('content')

    @if (session('status'))
        <div class="has-text-success">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.update') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="field">
            <label class="label">{{ __('Email') }}</label>
            <div class="control is-clearfix">
                <input type="email" autocomplete="on" name="email" value="{{ old('email', $request->email) }}" required="required" autofocus="autofocus" class="input">
            </div>
        </div>

        @error('email')
            <p class="help is-danger">{{ $message }}</p>
        @enderror

        <div class="field">
            <label class="label">Mot de passe</label>
            <div class="control is-clearfix">
                <input type="password" autocomplete="new-password" name="password" required="required" class="input">
            </div>
        </div>

        @error('password')
            <div class="help is-danger">{{ $message }}</div>
        @enderror

        <div class="field">
            <label class="label">Confirmez le mot de passe</label>
            <div class="control is-clearfix">
                <input type="password" autocomplete="new-password" name="password_confirmation" required="required" class="input">
            </div>
        </div>

        <hr>

        <div class="field">
            <div class="field-body">
                <div class="field is-grouped">
                    <div class="control">
                        <button type="submit" class="button is-black"> Réinitialiser le mot de passe </button>
                    </div>
                    <div class="control">
                        <a href="{{ route('login') }}" class="button is-outlined is-black">Connexion</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
    
@endsection
