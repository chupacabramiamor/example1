<!doctype html>
<html class="no-js" lang="">

<head>
  <meta charset="utf-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{title}</title>
  <meta name="description" content="">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link rel="stylesheet" href="/css/main.css">

</head>

<body>
  <div class="page-auth">
    <div class="page-auth__header">
      <img src="img/logoAuth.svg" alt="">
    </div>

    <div class="page-auth__form-wrap">
      <h3 class="page-auth__title">Sign In</h3>
      <div class="page-auth__subtitle">
        Don’t have an account?
        <a href="/login/signup" class="link-orange">
          Sign Up
        </a>
      </div>

      <div class="page-auth__login-with-google">
        <button class="btn-google-auth" type="button">
          <img src="img/google-logo.svg" alt="" class="icon-google">
          Sign In with Google
        </button>
      </div>

      <div class="page-auth__or">
        <span>or</span>
      </div>

      <form>
        <div class="page-auth__control">
          <span class="page-auth__control-icon">
            <img src="img/envelope.svg" alt="" class="icon-envelope">
          </span>
          <input type="email" class="input" placeholder="Email address">
        </div>

        <div class="page-auth__control">
          <span class="page-auth__control-icon">
            <img src="img/lock.svg" alt="" class="icon-lock">
          </span>
          <input type="password" class="input" placeholder="Password">
          <span class="page-auth__control-error hidden">
            <img src="img/attention.svg" alt="" class="icon-attention">
            <div class="page-auth__control-error-msg">Wrong Password</div>
          </span>
        </div>

        <div class="page-auth__control">
          <span class="page-auth__control-icon">
            <img src="img/lock.svg" alt="" class="icon-lock">
          </span>
          <input type="password" class="input input_error" placeholder="Password">
          <span class="page-auth__control-error">
            <img src="img/attention.svg" alt="" class="icon-attention">
            <div class="page-auth__control-error-msg">Wrong Password</div>
          </span>
        </div>

        <div class="page-auth__controlCheckbox">
          <label class="checkbox">
            <input type="checkbox" checked />
            <span class="checkbox__text">Remember Me</span>
            <span class="checkbox__icon">
              <svg width="12" height="9" viewBox="0 0 12 9" fill="none" xmlns="http://www.w3.org/2000/svg" style="width: 1.2rem; height: .9rem">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M4.47117 6.52868L10.6665 0.333344L11.6092 1.27601L4.47117 8.41401L0.666504 4.60934L1.60917 3.66668L4.47117 6.52868Z" fill="#FF5B29" />
              </svg>

            </span>
          </label>
          <a href="/login/forgot" className="link-orange">
            Forgot Password?
          </a>
        </div>

        <div class="page-auth__controlAction">
          <button type="submit" class="btn-primary">SIGN IN</button>
        </div>
      </form>
    </div>

    <div class="page-auth__footer">
      <div class="page-auth__copyright">
        Datafender&copy;2020 All Rights Reserved.
      </div>

      <ul class="page-auth__links">
        <li>
          <a href="//google.com">
            Cookie Preferences
          </a>
        </li>
        <li>
          <a href="//google.com">
            Privacy Policy
          </a>
        </li>
        <li>
          <a href="//google.com">
            Terms of Conditions
          </a>
        </li>
      </ul>
    </div>
  </div>


  <script src="/js/plugins.js"></script>
  <script src="/js/main.js"></script>

</body>

</html>