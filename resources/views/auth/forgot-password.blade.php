@extends('layouts.auth')

@section('card-header-title', 'Réinitialiser le mot de passe')

@section('content')

    <div class="">
        Mot de passe oublié ? Aucun problème. Faites-nous simplement savoir votre adresse e-mail et nous vous enverrons un lien de réinitialisation de mot de passe qui vous permettra d'en choisir un nouveau.
    </div>

    @if (session('status'))
        <div class="has-text-success">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
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

        <hr>

        <div class="field">
            <div class="field-body">
                <div class="field is-grouped">
                    <div class="control">
                        <button type="submit" class="button is-black"> Envoyer le lien</button>
                    </div>
                    <div class="control">
                        <a href="{{ route('login') }}" class="button is-outlined is-black">Connexion</a>
                    </div>
                </div>
            </div>
        </div>

    </form>
@endsection
