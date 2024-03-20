# Publish Migrations
`php artisan vendor:publish --provider="Zaengle\LaravelSecurityNotifications\PackageServiceProvider" --tag="migrations"`

# Publish Views
`php artisan vendor:publish --provider="Zaengle\LaravelSecurityNotifications\PackageServiceProvider" --tag="views"`

# Disable Notifications
`SEND_SECURITY_NOTIFICATIONS=false`