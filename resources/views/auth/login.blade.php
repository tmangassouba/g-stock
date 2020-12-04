@extends('layouts.auth')

@section('card-header-title', 'Connexion')

@section('content')

    @if (session('status'))
        <div class="has-text-success">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="field">
            <label class="label">{{ __('Email') }}</label>
            <div class="control is-clearfix">
                <input type="email" autocomplete="on" name="email" value="{{ old('email') }}" required="required" autofocus="autofocus" class="input">
            </div>
        </div>

        @error('email')
            <p class="help is-danger">{{ $message }}</p>
        @enderror

        <div class="field">
            <label class="label">Mot de passe</label>
            <div class="control is-clearfix">
                <input type="password" autocomplete="on" name="password" required="required" class="input">
            </div>
        </div>
        

        <div class="field">
            <label class="b-checkbox checkbox is-thin">
                <input type="checkbox" true-value="true" value="false" name="remember">
                <span class="check is-black"></span>
                <span class="control-label"> Se souvenir de moi </span>
            </label>
        </div>

        <hr>

        <div class="field">
            <div class="field-body">
                <div class="field is-grouped">
                    <div class="control">
                        <button type="submit" class="button is-black"> Connexion </button>
                    </div>
                    <div class="control">
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="button is-outlined is-black">
                                Mot de passe oublié ?
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection