# 🍔 MBurger PHP SDK 🍔 

This package provides a simple interface between your Laravel Project and the MBurger CMS, helping you to retrieve data easily.

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

## 3.0 - Methods reference 

In the current version of our PHP SDK you can find only a few methods that you can implement in your code but they're so powerful that enable you to do pretty anything with MBurger CMS.

### 3.1 - Retrieve multiple Blocks with a single API call

> getBlocks(array $block_ids, $original_media = false, $params = [], $filters = [], $order_asc = 1, $cache_seconds = 0)

| Specification | Data Type | Description |
|---|---|---|
| block_ids | Array | Array with the IDs (integer) of the requested Blocks |
| original_media | Boolean | Indicate if you want the original media or the converted ones |
| params | Array | Array with the parameters you want to pass to the MBurger params variable. Check our API Reference for more informations |
| filters | Array | Array with the filters you want to pass to the MBurger filters variable. Check our API Reference for more informations |
| order_asc | Boolean | Express if you want the data in ascendent or descendent order |
| cache_seconds | Integer | Number of seconds you want to keep the API response stored in your local cache |

### 3.2 - Retrieve a single Block

> getBlock($block_id, $original_media = 0, $params = [], $filters = [], $order_asc = 1, $cache_seconds = 0)

| Specification | Data Type | Description |
|---|---|---|
| block_id | Integer | ID of the requested Block |
| original_media | Boolean | Indicate if you want the original media or the converted ones |
| params | Array | Array with the parameters you want to pass to the MBurger params variable. Check our API Reference for more informations |
| filters | Array | Array with the filters you want to pass to the MBurger filters variable. Check our API Reference for more informations |
| order_asc | Boolean | Declare if you want the data in ascendent or descendent order |
| cache_seconds | Integer | Number of seconds you want to keep the API response stored in your local cache |

### 3.2 - Retrieve a single Section

> getSection($secton_id, $original_media = 0, $params = [], $filters = [], $order_asc = 1, $cache_seconds = 0, $use_slug = 0)

| Specification | Data Type | Description |
|---|---|---|
| section_id | Integer | ID of the requested Section |
| original_media | Boolean | Indicate if you want the original media or the converted ones |
| params | Array | Array with the parameters you want to pass to the MBurger params variable. Check our API Reference for more informations |
| filters | Array | Array with the filters you want to pass to the MBurger filters variable. Check our API Reference for more informations |
| order_asc | Boolean | Express if you want the data in ascendent or descendent order |
| cache_seconds | Integer | Number of seconds you want to keep the API response stored in your local cache |
| use_slug | Boolean | Declare if you want to use the section slug instead of the ID to retrieve data |

## 4.0 - Support & Feedback

For support regarding MBurger, the SDK or any kind of feedback please feel free to contact us via  [support.mburger.cloud](http://support.mburger.cloud/)

## 5.0 - License
The MIT License (MIT). Please see [License File](./LICENSE) for more information.
