
# Alias module for Kohana Framework

This module allows you to make usefull and beautifull URLs for your service. 

You don't need more `/user/<id>` or `/article/<id>` cursors in routes. Now you can use simply `/donald` and `/victory` or `/pokemon-go` like addresses for different resources.

## User Guide
Article describing this HMVC feature placed on our website <a href="https://ifmo.su/alias-system">https://ifmo.su/alias-system</a>

To include module, just place it to `/modules` directory and push to `Kohana::modules()` array in `bootstrap.php`
```php
Kohana::modules(array(
        'aliases' => MODPATH . 'aliases', // Aliases for URLs
        ...
));
```

All you need after is to incule alias creation and updating in your logic:

### Creating 

```php
$alias         = Model_Alias::generateUri( $uri );
$resource_type = Model_Uri::ARTICLE; // yourn own resource type such as user, article, category and other
$resource_id   = 12345;

$article->uri = Model_Alias::addAlias($alias, $resource_type , $resource_id);
```

### Updating 

```php
$resource_id   = $article->id;
$old_uri       = $article->uri;
$new_uri       = Model_Alias::generateUri( $uri );
$resource_type = Model_Uri::ARTICLE;


$article->uri = Model_Alias::updateAlias($old_uri, $new_uri, Model_Uri::ARTICLE, $resource_id);
```

## What about cache

Note that module's version does not includes cache scheme. You may need `memcache` or another driver to add this feature.

## Repository 
<a href="https://github.com/codex-team/kohana-aliases/">https://github.com/codex-team/kohana-aliases/</a>


## About us
We are small team of Web-developing fans consisting of IFMO students and graduates located in St. Petersurg, Russia. 
Fell free to give us a feedback on <a href="mailto::team@ifmo.su">team@ifmo.su</a>


