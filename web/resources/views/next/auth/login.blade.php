{{--
    Login screen (non-API).
    Extends from `next.base`.
--}}

@extends('next.base', [
    'title' => 'Login'
])

@push('scripts')
    @vite('login.ts')
@endpush

@section('app')
    <div id="app_login">
        <div id="login_panel" class="light">
            <a href="/" title="{{ __('frame.GOTO_HOME_PAGE') }}">
                <img src="/files--static/media/logo.min.svg">
            </a>
            <hr>
            <div id="login_form_container">
            </div>
            <a id="login_create_account" href="/user-services/register">
                {{ __("account_panel.CREATE_ACCOUNT") }}
            </a>

            {{-- gets placed _outside_ of the panel via styling --}}
            <div id="login_links">
                {{-- TODO: link to actual pages --}}
                <a href="/terms">{{ __('frame.footer.TERMS') }}</a>
                <a href="/privacy">{{ __('frame.footer.PRIVACY') }}</a>
                <a href="/docs">{{ __('frame.footer.DOCS') }}</a>
                <a href="/security">{{ __('frame.footer.SECURITY') }}</a>
            </div>
        </div>
    </div>
@endsection
