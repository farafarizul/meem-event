<x-guest-layout>
    @if (session('status'))
        <div class="alert alert-success mb-3" role="alert">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger mb-3" role="alert">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="is-alter">
        @csrf

        <div class="form-group">
            <div class="form-label-group">
                <label class="form-label" for="email">Email Address</label>
            </div>
            <div class="form-control-wrap">
                <input id="email" type="email"
                       class="form-control form-control-lg @error('email') is-invalid @enderror"
                       name="email"
                       value="{{ old('email') ?? 'admin@meem.com.my' }}"
                       required autofocus autocomplete="username"
                       placeholder="Enter your email address">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="form-group">
            <div class="form-label-group">
                <label class="form-label" for="password">Password</label>
            </div>
            <div class="form-control-wrap">
                <a href="#" class="form-icon form-icon-right passcode-switch lg" data-target="password">
                    <em class="passcode-icon icon-show icon ni ni-eye"></em>
                    <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
                </a>
                <input id="password" type="password"
                       class="form-control form-control-lg @error('password') is-invalid @enderror"
                       name="password"
                       value="12345678"
                       required autocomplete="current-password"
                       placeholder="Enter your password">
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="form-group">
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="remember_me" name="remember">
                <label class="custom-control-label" for="remember_me">Remember me</label>
            </div>
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-lg btn-primary btn-block">Sign in</button>
        </div>
    </form>
</x-guest-layout>
