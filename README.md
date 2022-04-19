# Optime Acl Bundle
Bundle para manejo de control de accesos.

## Instalación

```
composer require "XXX/XXX" "^1.0@dev"
```

## Configuración 

Agregar como un bundle en el `config/bundles.php`:

```php
<?php

return [
    ...
    Optime\Acl\Bundle\OptimeAclBundle::class => ['all' => true],
];
```

#### Configuración de opciones:

Crear/Ajustar el archivo `config/packages/optime_acl.yaml`:

```yaml
# Por ahora sin nada que agregar, no se necesita crea el archivo
```

Crear el archivo `config/routes/optime_acl.yaml`:

```yaml
optime_acl:
    resource: "@OptimeAclBundle/src/Controller/"
    type:     annotation
    prefix:   /{_locale}/admin/access-control
```

Correr comando de doctrine:

```
symfony console doctrine:schema:update -f
```

<hr>