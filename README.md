<p align="center">
    <img width="300" src="http://i.imgur.com/wnQwfRh.png"><br>
    <a><img src="https://img.shields.io/github/license/mashape/apistatus.svg?style=flat-square"></a>
    <a href="https://gitter.im/laravel-lux/Lobby?utm_source=share-link&utm_medium=link&utm_campaign=share-link">
      <img src="https://img.shields.io/gitter/room/laravel-lux/laravel-lux.svg?style=flat-square">
    </a>
</p>

## Get started
<pre>
$ git clone https://github.com/endlessdev/Lux.git
$ cd Lux && composer install && composer update
</pre>

## API Overview
### Authorization

| Method | URL | DESC |
|---|---|---|
| POST | v@/auth/ | Log-in |
| POST | v@/auth/{snsType?} | SNS Log-in |
| POST | v@/auth/signup | Register |
| POST | v@/auth/signup/{snsType?} | SNS Register |
| POST | v@/auth/signout | Log-out |
| GET | v@/auth/refresh | Refresh token |
| GET | v@/auth/info | Get auth info |
| DELETE | v@/auth/ | Withdrawal |

### User

| Method | URL | DESC |
|---|---|---|
| GET | v@/users/{page?} | Get user info (pagination) |
| GET | v@/user/{userIdx?} | Get specific user info |
| POST | v@/user/{userIdx?}/disable | Disable user |
| POST | v@/user/{userIdx?}/enable | Enable user |
| PUT | v@/user/{userIdx?} | Edit user info |
| DELETE | v@/user/{userIdx?} | Force withdrawal [Required Admin permission] |
