# 🍔 MBurger PHP SDK 🍔 

This package provide a simple interface from your Laravel Project to MBurger CMS, helping you retrieving data easily.

## 1.0 Installation

This package can be installed via Composer:

    composer require mumble/mburger

## 2.0 Configuration

There are only a few steps separating you from using our PHP SDK.

### 2.1 Publish the ServiceProvider

First of all you need to publish the Service Provider with the MBurger configuration that you'll find under `config/mburger.php`

    php artisan vendor:publish --provider="Mumble\MBurger\MBurgerServiceProvider"

### 2.2 Project API Key

Place your API KEY in your `.env` file like that

    MBURGER_API_KEY=1234567890ABCDEFGHI

If you don't have an API KEY yet, please generate one under your project settings page.

## 3.0 How to use

In the current version of our PHP SDK you can find only a few methods that you can implement in your code but they're so powerful that enable you to do pretty anything with MBurger CMS.

The SDK will return an array representing the response. By default methods that returns a list of objects are limited to 25 elements. See below for how to use pagination.

This SDK offers a fluent syntax composed of these parts:

1. Class instance
2. Parameters or Modifiers
3. Desired function

Below are reported all parameters/modifiers and functions with some examples.

For a complete list of all available parameters we remand to the official API documentation https://docs.mburger.cloud/api-docs-1.

### 3.1 Instantiate the SDK

To instantiate the SDK simply use:

    $sdk = new MBurger();

or use the compact version (useful for chaining):

    $response = (new MBurger())->getProject();

### 3.2 Functions

To obtain a project

    $response = (new MBurger())->getProject();

To obtain a list of blocks

    $response = (new MBurger())->getBlocks();

To obtain a specific block by id

    $response = (new MBurger())->getBlock($block_id);

To obtain a list of sections

    $response = (new MBurger())->getSections($block_id);

To obtain a specific section by id or slug

    $response = (new MBurger())->getSection($block_id_or_slug);

### 3.3 Modifiers

Below a list of modifiers (or middle methods) useful to personalize the request.

#### 3.3.1 Locale

To request a specific locale use the `locale(string $locale)` modifier. Example:

    $response = (new MBurger())->locale($locale)->getProject();

If the requested locale is not present an automatic fallback will be done. If you want to force the fallback if the requested locale is not present or is empty use the `forceLocaleFallback()` modifier:

    $response = (new MBurger())->forceLocaleFallback()->getBlock();

#### 3.3.2 Pagination

To use pagination in functions that returns a list of items use the modifiers `skip(int $skip)` and `take(int $take)`. Example:

    $response = (new MBurger())->skip(5)->take(50)->getSections(100);

Defaults are 0 and 25. The response will include a meta field containing the info for pagination, like total items and actual index.  

#### 3.3.3 Including

To include relations on the response are available a set of convenience methods. This can be useful, for example, for obtaining in one request all the desired sections with the related elements.

> NOTE: Not all methods are compatible with all functions. The SDK will throw an `MBurgeInvalidRequestException` exception. Check our API references for more info.

> NOTE: loading a lot of relations in one request can have a negative impact on performances.

- `include(array $include)`: generic method, you can pass an array of desired relations.
- `includeBlocks()`: it will include _blocks_. Available only on `getProject()`.
- `includeSections()`: it will include _sections_. Available only on `getBlocks()` and `getBlock()`.
- `includeElements()`: it will include _elements_. Available only on `getSections()` and `getSection()`.
- `includeStructure()`: it will include _block_ structure. Available only on `getProject()`, `getBlocks()` and `getBlock()`.
- `includeBeacons()`: it will include beacons. Available only on `getProject()`, `getSections()` and `getSection()`.
- `includeContracts()`: it will include _blocks_. Available only on `getProject()`.

Example: get first 15 sections of block 100 with the related elements

    $response = (new MBurger())->take(15)->includeElements()->getSections(100);

#### 3.3.4 Sorting

To apply specific orders is available this method `sortBy(string $value, string $direction = 'asc')`. the first argument specify on which value do the sorting, and the second the direction. Example:

    $response = (new MBurger())->sortBy('created_at', 'desc')->take(15)->getSections(100);

The default sorting in by _id_ and ascending.

> NOTE: this method is only available on functions that returns a list of items.

#### 3.3.4 Filtering

To filter items are available a set of convenience methods.

> NOTE: Not all methods are compatible with all functions. The SDK will throw an `MBurgeInvalidRequestException` exception. Check our API references for more info.

> NOTE: these methods are only available on functions that returns a list of items.

- `filterByIds(array $ids)`: it filters based an array on id with an exact match. Available only on `getBlocks()` and `getSections()`.
- `filterByRelation(int $block_id, int $section_id)`: it filters based on related _sections_. Available only on `getSections()`.
- `filterByValue(array $values, string $element_name = null)`: it filters based on array of values. Specifying the second parameter `element_name` the filtering in done only on _elements_ the match the name. Available only on `getSections()`.
- `filterByTitle(string $title)`: it filters based on title. Available only on `getBlocks()`.
- `filterByGeofence(float $latNE, float $latSW, float $lngNE, float $lngSW)`: it filters based on geofence rectangle. Available only on `getSections()`.

#### 3.3.5 Distance

If you have _sections_ with elements of type _address_ (which automatically contains coordinates) you can obtain your distance from the "_section_" (like a POI) with the method `istance(float $latitude, float $longitude)` by providing your coordinates. Example:

    $response = (new MBurger())->distance(22.231232, 16.325322)->getSections(100);

> NOTE: this method is only available on functions `getSections()`.

#### 3.3.6 Slug

Normally a _section_ is retrieved by _id_ or _slug_. MBurger tries to infer which method to use based on the type of the parameter: if it's numeric it will use the _id_, if it's a string it will use the _slug_. Is possible to force using the _slug_ with the following method `forceSlug()`. Example:

    $response = (new MBurger())->locale('en')->forceSlug()->getSection('321287');

> NOTE: this method is only available on functions `getSection()`.

### 3.4 Errors

MBurger SDK use an exception based error system, in case of error it will throw an exception with a descritive message.

### 3.5 Cache

Every function contains a method `cache(int $cache_ttl = 0)` to automatically cache the response. The TTL is in seconds. Example:

    $response = (new MBurger())->cache(300)->getSections(100);

## 4.0 Support & Feedback

For support regarding MBurger, the SDK or any kind of feedback please feel free to contact us via  [support.mburger.cloud](http://support.mburger.cloud/)

## 5.0 License
The MIT License (MIT). Please see [License File](./LICENSE) for more information.
