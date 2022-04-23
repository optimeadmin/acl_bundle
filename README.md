# Optime Acl Bundle

Bundle para manejo de control de accesos.

## Instalación

```
composer require "optimeconsulting/acl_bundle" "^1.0@dev"
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
optime_acl:
    # Se puede deshabilitar para pruebas
    enabled: true

    # Opcional. Se puede indicar un servicio propio para manejo de roles/perfiles
    # Dicho servico debe implementar la interfaz \Optime\Acl\Bundle\Security\User\RolesProviderInterface
    # Por defecto usa los roles del security.yaml
    # roles_provider: LoyaltyRolesProvider 

    # Prefijos de Namespace donde se van a buscar los recursos/referencias del proyecto
    # Son Namespaces de los controladres del proyecto y de los
    # diferentes bundles que esté usando la app.
    resources:
        - App\   # Ruta del propio proyecto
        - Optime\ # Ruta de todo lo que comienze con el namespace Optime
        # - "" # Con esto registramos todas las posibles rutas del proyecto y bundles como recursos.
        # - Optime\Acl\Bundle\ # Ejemplo de Bundle especifico

    # Opcional. Se puede indicar nombres de Controladores o namespaces a excluir de los
    # posibles recursos de seguridad en la aplicación.
    # excluded_resources:
    #    - App\Controller\SecurityController* # Excluye todo lo que comienze por el valor dado.
    #    - *Email\Bundle*                     # Excluye todo lo que contenga el valor dado.
    #    - *UserController::index             # Excluye lo que termine por el valor dado.
```

Crear el archivo `config/routes/optime_acl.yaml`:

```yaml
optime_acl:
    resource: "@OptimeAclBundle/src/Controller/"
    type: annotation
    prefix: /{_locale}/admin/access-control
```

Correr comando de doctrine:

```
symfony console doctrine:schema:update -f
```

<hr>

## Uso

Luego de configurado el bundle, se puede acceder a la ruta `/{_locale}/admin/access-control`
Y allí se podrá configurar todo lo necesario para permisologías a cada controlador de la app.

Basicamente son tres los terminos que maneja el bundle y que son necesarios entender para hacer un uso correcto de la
herramienta:

* Roles: serán los tipos de usuario o perfiles que acceden en la app.
* Referencias: són los controladores y acciones a los que acceden los usuarios desde el navegador.
* Recursos: Son nombres que le damos a un conjunto de referencias (controladores) para configurarles la permisología. **
  Los roles son los que le relacionamos a estos recursos**.

Por ejemplo, podemos tener el recurso `user list` al cual solo accede el rol `ROLE_ADMIN` e internamente dicho recurso
estará relacionado al rol indicado y a ciertas referencias (controladores) como:

* App\Controller\UserController::list
* App\Controller\UserController::listInactivates
* App\Controller\UserController::showUserData

Mientras que otro recurso `user edit` será accedido por el rol `ROLE_SUPER_ADMIN` e internamente estará relacionado con
las referencias (controladores):

* App\Controller\UserController::create
* App\Controller\UserController::edit
* App\Controller\UserController::changeStatus
* App\Controller\UserController::delete

### Verificación de accesos

El acceso a los recursos se valida haciendo uso del sistema de seguridad de Symfony. Más especificamente haciendo uso de
los [Voters de seguridad](https://symfony.com/doc/current/security/voters.html).

Hay dos formas de verificar el acceso a un recurso:

    * Por nombre de recurso.
    * Por nombre de ruta.

Ejemplos:

```php

# Controlador:
public function edit() {
    # Por el nombre de un recurso
    if (!$this->isGranted("resource", "user edit")) {
        die("No tiene acceso");
    }
    
    // Por el nombre de una ruta
    if (!$this->isGranted("resource_route", "ruta_usuario_editar")) {
        die("No tiene acceso");
    }
}

# Atributo en controlador:
#[IsGranted("resource", "user")]
public function edit() {
    # ...
}
```

En las plantillas twig:

```jinja
{% if is_granted("resource", "user edit") %}
    <a href="{{ path("ruta_user_edit", {id: item.id}) }}">Edit</a>
{% endif %}

{% if is_granted("resource", "user") %}
    {# 
    Si solo preguntamos si tiene acceso a user, será verdadero si tiene acceso a algún 
    recurso hijo de user o directamente a user 
    #}
    <a href="{{ path("ruta_user_show", {id: item.id}) }}">Show</a>
{% endif %}

{% if is_granted("resource_route", "ruta_user_show") %}
    <a href="{{ path("ruta_user_show", {id: item.id}) }}">Show</a>
{% endif %}

{% if is_granted("resource_route", "ruta_user_edit") %}
    <a href="{{ path("ruta_user_edit", {id: item.id}) }}">Edit</a>
{% endif %}
```

La forma más flexible de usar la verificación es con las rutas más que con los nombres de recurso. Ya que así no
tendremos que hacer cambios de código a futura si alguna ruta cambia de nombre de recurso en la configuración del
backend.