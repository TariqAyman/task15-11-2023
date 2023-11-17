<?php
/*
 * Created by PhpStorm.
 * Developer: Tariq Ayman ( tariq.ayman94@gmail.com )
 * Date: 4/14/22, 12:03 AM
 * Last Modified: 4/14/22, 12:03 AM
 * Project Name: GenCode
 * File Name: api-response.php
 */

use Carbon\CarbonInterface;

return [

    /*
   |--------------------------------------------------------------------------
   | Resources options
   |--------------------------------------------------------------------------
   |
   | These are the `resource` options that can be used with any `Resource` class
   | The `assets` option defines the generating `url` for any asset, by default is `url()`
   |
   | The date key provides the date options that can be used for any date column
   | `format`: the date format that will be returned.
   | `timestamp`: if set to true, the unix timestamp will be returned as integer.
   | `human`: if set to true, a human time will be returned i.e 12 minutes ago.
   | `intl`: Display formatted date in locale text
   |
   |  Please note that if the timestamp and human time are set to true, the
   |  date format will be returned as string, otherwise it will be returned as array`object`.
   |
   */
    'resources' => [
        'assets' => 'url',
        'date' => [
            'format' => 'd-m-Y h:i:s a',
            'timestamp' => true,
            'humanTime' => true,
            'intl' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | General Configurations
    |--------------------------------------------------------------------------
    |
    | The serialize_precision option if set to -1 will encode the float numbers properly
    |
    */
    'serialize_precision' => -1,

    /*
    |--------------------------------------------------------------------------
    | Locale Codes List
    |--------------------------------------------------------------------------
    |
    | This will determine all available locale codes in the application
    | It will be used to generate translation files when generating new module
    |
    */
    'localeCodes' => [
        'en',
        'ar',
    ],

    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    |
    | You can define your custom validation rules in the `rules` array by defining the rule name as array key
    | and the value will be the rule class.
    | If you want to specify which method to be called on validation, you can define the method name as array [class, methodName].
    | Default method name is `passed`
    |
    */
    'validation' => [
    ],

    /*
    |--------------------------------------------------------------------------
    | Localization Mode
    |--------------------------------------------------------------------------
    |
    | This will determine the type of handing data that has multiple values based on locale code
    | Mainly it will be used with resources when returning the data
    |
    | Available options: array|object
    */
    'localizationMode' => 'array',

    /*
    |--------------------------------------------------------------------------
    | Date options
    |--------------------------------------------------------------------------
    |
    | Carbon Immutable
    | By setting it to true, all carbon instances from `now()` and `today()` will be immutable,
    | this means whenever you call the `now()` with any appended methods, it will return a new instance of carbon
    | defaults to true starting from v2.15.0
    | i.e Date::now()->addDays(1) will return a new instance of carbon
    | It's highly recommended to use \Illuminate\Support\Facades\Date instead of Carbon
    |
    | Week Starts At and Ends at are useful to set the week start and end days
    |
    | Defaults to: week_starts_at = Saturday, week_ends_at = Friday
    */
    'date' => [
        'immutable' => true,
        'week_starts_at' => CarbonInterface::SATURDAY,
        'week_ends_at' => CarbonInterface::FRIDAY,
    ],

    /*
   |--------------------------------------------------------------------------
   | Response Options
   |--------------------------------------------------------------------------
   | badRequest Response Map strategy
   |
   | If the response map strategy is set as array, then it will be returned as array of objects
   | each object looks like [key => input, value => message]
   | However, key and value can be customized as well.
   |
   | Available Options: `array` | `object`, defaults to `array`
   |
   | The `key` will set the name of object key that will hold the input name, defaults to `key`
   | The `value` will set the name of object key that will hold the error message itself, defaults to `value`
   |
   */
    'response' => [
        'errors' => [
            'strategy' => 'array',
            'key' => 'key',
            'value' => 'message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Events list
    |--------------------------------------------------------------------------
    |
    | Set list of events listeners that will be triggered later from its sources
    |
    */
    'events' => [
//        'response.send' => [
//            [WithAuthAccount::class, 'sendUser'],
//        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Base filters
    |--------------------------------------------------------------------------
    |
    */
    'filters' => [

    ],
];
