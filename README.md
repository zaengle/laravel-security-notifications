![Tests](https://github.com/zaengle/laravel-security-notifications/workflows/Tests/badge.svg?branch=main)
[![MIT Licensed](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)

![background hero](hero-image.jpg)

# Laravel Security Notifications

This package adds an out-of-the-box, fully customizable solution for sending security notifications to users when their account is updated or accessed from a new device.

## Installation

`composer require zaengle/laravel-security-notifications`

## Testing

`./vendor/bin/pest`

## Setup

### Publish Configuration

`php artisan vendor:publish --provider="Zaengle\LaravelSecurityNotifications\Providers\PackageServiceProvider" --tag="config"`

### Use Trait

In order to send security notifications, you'll need to add the `Securable` trait to your user model. Additionally, you'll want to make sure you are using Laravel's `Notifiable` trait.

```php
<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Zaengle\LaravelSecurityNotifications\Traits\Securable;

class User extends Authenticatable
{
    use Securable, Notifiable;
}
```

## Basic Usage

### Securing Fields

The `Securable` trait watches your user model for updates to the specified secure fields. By default, the secure fields are `email`, `username`, and `password`. If any of these fields are updated, a security notification will be sent to the user. The original email address will be notified in the event that the `email` field is updated.

You may customize the secure fields by overriding the `getSecureFields` method on your user model.

```php
public function getSecureFields(): array
{
    return [
        'first_name',
        'last_name',
        'email',
    ];
}
```

### Securing Logins

The `Securable` trait also provides a `logins` relationship that can be used to track user logins. This relationship tracks all IP addresses that have logged into the user's account. In order to set up secure logins, you'll need to publish and run the migration included in the package to set up a `logins` table. This table name is configurable in the published `security-notifications.php` config file.

```console
php artisan vendor:publish --provider="Zaengle\LaravelSecurityNotifications\Providers\PackageServiceProvider" --tag="migrations"

php artisan migrate
```

You can use the `IPAddress` facade to track user logins in your existing login controller. If the given IP address has already been used, it will update the login record with the login time. If the IP address is new, it will create a new login record and send a notification to the user.

```php
<?php

namespace App\Http\Controllers;

use Zaengle\LaravelSecurityNotifications\Facades\IPAddress;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        // Your existing login logic
        
        IPAddress::process([
            'ipAddress' => $request->ip(),
            'userId' => auth()->id(),
            'userType' => auth()->user()->getMorphClass(),
        ]);
        
        // return
    }
}
```

### Disable Notifications

If you would like to disable sending notifications you can set the following config value:

`'send_notifications' => false`

### Customizing Notification Templates

#### Custom Email Templates

The package provides email notification templates out-of-the-box. However, if you would like to customize these templates to match your system, you may publish the views to your application and override them.

`php artisan vendor:publish --provider="Zaengle\LaravelSecurityNotifications\Providers\PackageServiceProvider" --tag="views"`

They will be available under `resources/views/vendor/security-notifications`.

There are two views that can be customized. Each with access to specific data:
- `security-alert.blade.php`
  - Has access to a `$fields` array which contains the fields that were updated. It also has access to the `$updated_at` variable which is a Carbon instance of when the fields were updated.
- `login-from-new-ip.blade.php`
  - Has access to a `$login` variable which is an instance of the `Zaengle\LaravelSecurityNotifications\Models\Login` model.

#### Custom Notification Classes

If you would like to further customize the notifications, you can configure the notification classes in the `security-notifications.php` config file.

```php
'notifications' => [
    'secure_login' => \Zaengle\LaravelSecurityNotifications\Notifications\LoginFromNewIP::class,
    'secure_fields' => \Zaengle\LaravelSecurityNotifications\Notifications\SecureFieldsUpdated::class,
],
```

### Custom IP Address Driver

If you would like to have full control over IP address and login handling, you can create a custom driver by implementing the `Zaengle\LaravelSecurityNotifications\Services\DigestIPAddress` interface like the example below.

```php
<?php

use Zaengle\LaravelSecurityNotifications\Services\DigestIPAddress;

readonly class CustomIPAddressDriver implements DigestIPAddress
{
    public function __construct(
        private readonly string $ipAddress,
        private readonly string $customField,
    )
    {
    }

    public function handle(): void
    {
        // Custom logic to handle IP address
    }
}
```

Be sure to update the `ip_address_driver` config value to point to your custom driver.

```php
'ip_address_driver' => \Path\To\CustomIPAddressDriver::class,
```

This will tell the `IPAddress` facade to use your custom driver. Simply pass an array of data to `IPAddress::process()` matching your `__construct` definition.

```php
IPAddress::process([
    'ipAddress' => $request->ip(),
    'customField' => 'customValue',
]);
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Credits

- [Header Image](https://unsplash.com/photos/a-blurry-photo-of-lights-in-the-dark-AHBNGvRTm_A)