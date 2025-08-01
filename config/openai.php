<?php

return [

    /*
    |--------------------------------------------------------------------------
    | OpenAI API Key
    |--------------------------------------------------------------------------
    |
    | Your API key from https://platform.openai.com/account/api-keys
    | This will be pulled from your .env file.
    |
    */
    'api_key' => env('OPENAI_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | OpenAI Organization (optional)
    |--------------------------------------------------------------------------
    |
    | If you belong to multiple organizations, set your org ID here.
    |
    */
    'organization' => env('OPENAI_ORG_ID', null),

    /*
    |--------------------------------------------------------------------------
    | Default Model
    |--------------------------------------------------------------------------
    |
    | You can override the default chat model here if you like.
    |
    */
    'default_model' => env('OPENAI_DEFAULT_MODEL', 'gpt-3.5-turbo'),
];
