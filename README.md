# Terms Bundle

## Installation

```sh
composer require itk-dev/terms-bundle:4.x-dev
```

Enable the bundle in `config/bundles.php`:

```php
<?php

return [
    …,
    ItkDev\TermsBundle\ItkDevTermsBundle::class => ['all' => true],
];
```

Add routes in `config/routes/terms_bundle.yaml`, say:

```yaml
terms_bundle:
    resource: '@ItkDevTermsBundle/Resources/config/routing.xml'
    prefix: /terms
```

## Configuration

Check default bundle configuration

```sh
bin/console config:dump-reference ItkDevTermsBundle
```

Make any necessary changes in `config/packages/terms_bundle.yaml`, say.

**Important**: Make sure that `itk_dev_terms.path` is set correctly to match
your setup.

Add a `termsAcceptedAt` (or whatever you've set
`itk_dev_terms.user_terms_property` to) property to your `User` entity, e.g.

```php
    /**
     * @var \DateTime
     * @ORM\Column(name="terms_accepted_at", type="datetime", nullable=true)
     */
    protected $termsAcceptedAt;
```

## Displaying terms and conditions

Set the config parameter `itk_dev_terms.user_terms_content_path` to a `twig`
template file containing your actual terms and conditions, e.g.

```
# .env.local
TERMS_CONTENT_PATH='%kernel.project_dir%/misc/terms/content.html.twig'

# config/packages/itk_dev_terms_bundle.yaml
itk_dev_terms:
    path: ^/
    user_terms_content_path: '%env(resolve:TERMS_CONTENT_PATH)%'
```

Override the default terms template and use `terms_render()` to render the terms
template.

```twig
# templates/bundles/ItkDevTermsBundle/Default/index.html.twig

<div class="terms">
  {{ terms_render() }}
</div>
```
