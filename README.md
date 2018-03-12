# Terms Bundle

## Installation

```sh
composer require itk-dev/terms-bundle dev-master
```

Enable the bundle in `app/AppKernel.php`:

```php
public function registerBundles() {
    $bundles = [
        // …
        new ItkDev\TermsBundle\ItkDevTermsBundle(),
    ];
    // …
}
```

Add routes in `app/config/routing.yml', say:

```yaml
terms_bundle:
    resource: '@ItkDevTermsBundle/Resources/config/routing.xml'
    prefix: /terms
```

Check default bundle configuration

```sh
bin/console config:dump-reference ItkDevTermsBundle
```

If the default configuration does not match your setup it can be
modified in `app/config/config.yml`.
