# API Routes (Auto-generated)

**Generated:** 2025-06-07 13:50:34 UTC

## All Registered Routes

```

  GET|HEAD  / ...................................................................................... home
  GET|HEAD  confirm-password password.confirm › App\Portal\Auth\Controllers\ConfirmablePasswordControlle…
  POST      confirm-password ............ App\Portal\Auth\Controllers\ConfirmablePasswordController@store
  GET|HEAD  dashboard ......................................................................... dashboard
  POST      email/verification-notification verification.send › App\Portal\Auth\Controllers\EmailVerific…
  GET|HEAD  forgot-password password.request › App\Portal\Auth\Controllers\PasswordResetLinkController@c…
  POST      forgot-password password.email › App\Portal\Auth\Controllers\PasswordResetLinkController@sto…
  GET|HEAD  login ............. login › App\Portal\Auth\Controllers\AuthenticatedSessionController@create
  POST      login ...................... App\Portal\Auth\Controllers\AuthenticatedSessionController@store
  POST      logout .......... logout › App\Portal\Auth\Controllers\AuthenticatedSessionController@destroy
  GET|HEAD  register ............. register › App\Portal\Auth\Controllers\RegisteredUserController@create
  POST      register ......................... App\Portal\Auth\Controllers\RegisteredUserController@store
  POST      reset-password ..... password.store › App\Portal\Auth\Controllers\NewPasswordController@store
  GET|HEAD  reset-password/{token} password.reset › App\Portal\Auth\Controllers\NewPasswordController@cr…
  ANY       settings ............................................ Illuminate\Routing › RedirectController
  GET|HEAD  settings/appearance .............................................................. appearance
  GET|HEAD  settings/password ... password.edit › App\Portal\Settings\Controllers\PasswordController@edit
  PUT       settings/password password.update › App\Portal\Settings\Controllers\PasswordController@update
  GET|HEAD  settings/profile ...... profile.edit › App\Portal\Settings\Controllers\ProfileController@edit
  PATCH     settings/profile .. profile.update › App\Portal\Settings\Controllers\ProfileController@update
  DELETE    settings/profile profile.destroy › App\Portal\Settings\Controllers\ProfileController@destroy
  GET|HEAD  storage/{path} ................................................................ storage.local
  GET|HEAD  up .......................................................................................... 
  GET|HEAD  verify-email verification.notice › App\Portal\Auth\Controllers\EmailVerificationPromptContro…
  GET|HEAD  verify-email/{id}/{hash} verification.verify › App\Portal\Auth\Controllers\VerifyEmailContro…

                                                                                      Showing [25] routes
```

