# neoan3 caching app

This class will utilize the output buffer to establish basic caching.
This is especially useful if you render static pages server-side after computational procedures.

## Installation

`composer require neoan3-apps/cache`

### Usage

1. Set caching as early as possible during execution. In neoan3, the construct of the used frame is ideal.

    ```PHP 
    function __construct() {
        Neoan3\Apps\Cache::setCaching('-2 hours');
        parent::__construct();
    }
    ```

2. Write the output after rendering your application. In a neoan3 frame:

    ```PHP
    // overwrite output method
    function output($params = []) {
        parent::output($params);
        Neoan3\Apps\Cache::write();
    }
    ```

## Methods

### setCaching

`Neoan3\Apps\Cache::setCaching( bool | strtotime-expression )`

If set to true, caching will generate an "immortal" cache. When a strtotime expression is used,
the cache is invalidated AFTER the request was loaded beyond the lifespan.

### write

`Neoan3\Apps\Cache::write()`

Writes the output buffer to a cached file in the respective component.

### invalidate

`Neoan3\Apps\Cache::invalidate( string )`

Takes the name of a component folder as string to delete all cache-files of a particular component.

### invalidateAll

`Neoan3\Apps\Cache::invalidateAll()`

Clears all cache-files in the project.
