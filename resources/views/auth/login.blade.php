@extends('layouts.app')

@section('title', __('messages.login_title'))

@section('content')
<div class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-header">
            <div class="auth-logo">
                <div class="brand-icon" style="width: 44px; height: 44px; border-radius: 12px;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="5 3 19 12 5 21 5 3"></polygon>
                    </svg>
                </div>
            </div>
            <h1 class="auth-title">{{ __('messages.login_title') }}</h1>
            <p class="auth-subtitle">{{ __('messages.login_subtitle') }}</p>


        </div>

        @if ($errors->any())
            <div class="alert-danger" id="login-error-alert" style="margin-bottom: 1.25rem;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST" class="auth-form" id="login-form">
            @csrf

            <div class="form-group">
                <label for="username" class="form-label">{{ __('messages.username') }}</label>
                <div class="input-group">
                    <span class="input-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </span>
                    <input 
                        type="text" 
                        name="username" 
                        id="username" 
                        class="form-control has-icon" 
                        placeholder="{{ __('messages.username_placeholder') }}" 
                        value="{{ old('username') }}" 
                        required 
                        autofocus
                    >
                </div>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">{{ __('messages.password') }}</label>
                <div class="input-group">
                    <span class="input-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                    </span>
                    <input 
                        type="password" 
                        name="password" 
                        id="password" 
                        class="form-control has-icon" 
                        placeholder="{{ __('messages.password_placeholder') }}" 
                        required
                    >
                </div>
            </div>

            <div style="display: flex; align-items: center; justify-content: space-between;">
                <label class="form-check">
                    <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <span>{{ __('messages.remember_me') }}</span>
                </label>
            </div>

            <button type="submit" class="btn btn-primary" id="btn-submit-login" style="width: 100%; padding: 0.85rem;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                    <polyline points="10 17 15 12 10 7"></polyline>
                    <line x1="15" y1="12" x2="3" y2="12"></line>
                </svg>
                <span>{{ __('messages.login_button') }}</span>
            </button>


        </form>
    </div>
</div>
@endsection
