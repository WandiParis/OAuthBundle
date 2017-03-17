# OAuth wrapper for Symfony 3

OAuthBundle is a symfony 3 wrapper bundle for [Lusitanian/PHPoAuthLib](https://github.com/Lusitanian/PHPoAuthLib) 
which provides oAuth support in PHP 5.3+ and is very easy to integrate with any project which requires an oAuth client.

---
 
- [Supported services](#supported-services)
- [Installation](#installation)
- [Registering the Bundle](#registering-the-bundle)
- [Configuration](#configuration)
- [Services](#services)
- [More usage examples](#more-usage-examples)

## Supported services

The library supports both oAuth 1.x and oAuth 2.0 compliant services. A list of currently implemented services can be found below. More services will be implemented soon.

Included service implementations:

 - OAuth1
    - BitBucket
    - Etsy
    - FitBit
    - Flickr
    - Scoop.it!
    - Tumblr
    - Twitter
    - Xing
    - Yahoo
 - OAuth2
    - Amazon
    - BitLy
    - Box
    - Dailymotion
    - Dropbox
    - Facebook
    - Foursquare
    - GitHub
    - Google
    - Harvest
    - Heroku
    - Instagram
    - LinkedIn
    - Mailchimp
    - Microsoft
    - PayPal
    - Pocket
    - Reddit
    - RunKeeper
    - SoundCloud
    - Vkontakte
    - Yammer
- more to come!

To learn more about Lusitanian/PHPoAuthLib go [here](https://github.com/Lusitanian/PHPoAuthLib) 

## Installation

Add oauth-bundle to your composer.json file:

```json
"require": {
  "wandi/oauth-bundle": "~0.1"
}
```

Use composer to install this package.

```
$ composer update wandi/oauth-bundle
```

### Registering the Bundle

Register the bundle in your ```app/AppKernel.php```:

```php
    new \Wandi\OAuthBundle\WandiOAuthBundle(),
```

## Configuration

Now add required config to ```app/config/config.yml```: 

```yaml
wandi_oauth:
    resource_owners:
        Xing:
            client_id: thisismyclientid
            client_secret: thisismyclientsecret
```

**important**:The resource owner name has to be in correct casing. Have a look on the available constants in [ServiceFactory/ResourceOwners.php](/ServiceFactory/ResourceOwners.php)

Xing is used as an example here. Replace it with whatever your want. Now add all the resource owners you need, the services are created automatically.

# Services

Services will be created automatically by this bundle. In my case, i want the xing service:
 
```php
    $service = $this->container->get('wandi_oauth.service.xing');
```

or inject it into another service:

```php
    fancy_company.random_namespace.wayne_bundle:
        class: FancyCompany\Bundle\WayneBundle\MyCool\ClassFor\WorldDominance
        arguments:
            - "@wandi_oauth.service.xing"
```

---

### More usage examples:

How to use Lusitanian/PHPoAuthLib  [here](https://github.com/Lusitanian/PHPoAuthLib/tree/master/examples)

