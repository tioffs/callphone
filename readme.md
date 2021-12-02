# Laravel/Lumen Call Phone - Проверка номера телефона с помощью звонка SMS.RU [![License][packagist-license]][license-url]

[![Downloads][packagist-downloads]][packagist-url]


- [Installation](#Installation)
- [Example](#Example)
- Method
    - [phone](#Get-call-phone)
    - [check](#Check-status)


## Installation
**Using Composer:**
```
composer require tioffs/callphone
```
**Laravel config app.php**
```
'callphone' => ['api_key' => env('SMSRU_API_KEY', null)]
```
**lumen config app.php**
```
config(['callphone' => ['api_key' => env('SMSRU_API_KEY', null)]);
```
**Registre Service Provider:**
- lumen ``` $app->register(callphone\CallServiceProvider::class); ```
- laravel ``` config/app.php providers => [callphone\CallServiceProvider::class] ```
## Example
```php
Route::get('/call', function(Illuminate\Http\Request $request, callphone\Call $call){
     $phone = $call->phone($request->phone);
     return response()->json($phone);
});

/** response: **/
{
    "check_status":null,
    "status_code":100,
    "check_id":2222-3333,
    "call_phone":+79095001010,
    "error":null,
    "call_phone_pretty":null
}
```
# Method
## Get call phone
create a new number check, the method will return us the number to call within 5 minutes
```php
$phone string = 79095001010;
$call->phone($phone);
```
## Check status
Method for checking the call status
```php
$check_id string = $call->phone("79095001010")->check_id;
$call->check($check_id);
if ($call->check_status === 401) {
    /** the user called the number, the number is confirmed **/
}
```
----

Made with &#9829; from the @tioffs

[tioffs-url]: https://timlab.ru/
[license-url]: https://github.com/tioffs/callphone/blob/master/LICENSE

[packagist-url]: https://packagist.org/packages/tioffs/callphone
[packagist-license]: https://img.shields.io/github/license/tioffs/callphone
[packagist-downloads]: https://img.shields.io/packagist/dm/tioffs/callphone
