# 🍔 MBurger PHP SDK 🍔 

This package provide a simple interface from your Laravel Project to MBurger CMS, helping you retrieving data easily.

## 1.0 - Installation

This package can be installed via Composer:

    composer require mumble/mburger

## 2.0 - Configuration

There are only a few steps separating you from using our PHP SDK.

### 2.1 - Publish the ServiceProvider

First of all you need to publish the Service Provider with the MBurger configuration that you'll find under `config/mburger.php`

    php artisan vendor:publish --provider="Mumble\MBurger\MBurgerServiceProvider"

### 2.2 - Project API Key

Place your API KEY in your `.env` file like that

    MBURGER_API_KEY=1234567890ABCDEFGHI

If you don't have an API KEY yet, please generate one under your project settings page.

## 3.0 - Reference 

> Work in progress

## 4.0 - Support & Feedback

For support regarding MBurger, the SDK or any kind of feedback please feel free to contact us via  [support.mburger.cloud](http://support.mburger.cloud/)

## 5.0 - License
The MIT License (MIT). Please see [License File](./LICENSE) for more information.
