Claro, aquí tienes el contenido completo en formato Markdown, listo para que lo copies y lo pegues directamente en un archivo llamado `README.md` en la raíz de tu proyecto.

````markdown
# Integrador CMDB con Aleph Manager

Este es un proyecto web desarrollado con **CodeIgniter 4** que sirve como intermediario para gestionar registros de una CMDB (Base de Datos de Gestión de la Configuración) a través de la API de **Aleph Manager**.

La aplicación permite importar categorías desde la API, visualizar los registros de la CMDB asociados a cada categoría, y realizar operaciones de importación y exportación masiva de dichos registros mediante archivos (CSV, Excel).

## ✨ Características Principales

-   **Importación de Categorías**: Sincroniza las categorías de la CMDB desde la API de Aleph y las almacena en una base de datos local.
-   **Visualización de Registros**: Lista los registros de la CMDB filtrados por categoría.
-   **Exportación de Datos**: Genera un archivo (probablemente CSV o Excel) con todos los registros de una categoría específica.
-   **Importación de Datos**: Permite subir un archivo con nuevos registros o modificaciones para ser procesados y enviados a la API de Aleph.
-   **Arquitectura Modular**: El código está organizado en Controladores, Modelos, Librerías y Servicios, siguiendo las mejores prácticas de CodeIgniter.
-   **Comunicación Segura**: Utiliza una clave de API para autenticar las solicitudes contra Aleph Manager.

## 🛠️ Stack Tecnológico

-   **Framework**: [CodeIgniter 4](https://codeigniter.com/)
-   **Lenguaje**: PHP 8.x
-   **Base de Datos**: MySQL, MariaDB, o cualquier otra soportada por CodeIgniter.
-   **Dependencias**: [CodeIgniter CURLRequest](https://codeigniter.com/user_guide/libraries/curlrequest.html) para las llamadas a la API.

## 🚀 Instalación y Configuración

Sigue estos pasos para poner en marcha el proyecto en un entorno de desarrollo local.

### Prerrequisitos

-   PHP 8.0 o superior
-   Composer
-   Un servidor de base de datos (Ej. MySQL, MariaDB)

### Pasos

1.  **Clonar el repositorio:**
    ```bash
    git clone <URL_DEL_REPOSITORIO>
    cd <NOMBRE_DEL_PROYECTO>
    ```

2.  **Instalar dependencias de PHP:**
    ```bash
    composer install
    ```

3.  **Configurar el entorno:**
    Copia el archivo de entorno de ejemplo y configúralo.
    ```bash
    cp env .env
    ```
    Abre el archivo `.env` y ajusta las siguientes secciones:

    -   **URL del sitio** (importante para que CodeIgniter funcione correctamente):
        ```ini
        app.baseURL = 'http://localhost:8080'
        ```

    -   **Configuración de la Base de Datos**:
        ```ini
        database.default.hostname = localhost
        database.default.database = nombre_de_tu_db
        database.default.username = tu_usuario_db
        database.default.password = tu_password_db
        database.default.DBDriver = MySQLi
        ```

4.  **Ejecutar las migraciones de la base de datos:**
    Para crear la tabla `categorias` y otras que puedas necesitar, ejecuta el siguiente comando:
    ```bash
    php spark migrate
    ```
    *Nota: Asegúrate de tener un archivo de migración para la tabla `categorias`.*

5.  **Configurar la API de Aleph (¡Importante!)**
    Actualmente, la URL base y la clave de la API están fijas en el archivo `app/Libraries/AlephAPI.php`. **Se recomienda encarecidamente** moverlas al archivo `.env` por seguridad y flexibilidad.

    -   Añade estas líneas a tu archivo `.env`:
        ```ini
        aleph.api.baseUrl = '[https://qa.alephmanager.com/API/](https://qa.alephmanager.com/API/)'
        aleph.api.key = 'zkUMrLN8xKEtrCr4Y7hYfLw8k!utbb'
        ```

    -   Modifica el constructor de `app/Libraries/AlephAPI.php` para leer estas variables:
        ```php
        public function __construct()
        {
            $this->client = \Config\Services::curlrequest();
            // Cargar las variables desde el .env
            $this->base_url = getenv('aleph.api.baseUrl');
            $this->api_key = getenv('aleph.api.key');
        }
        ```

6.  **Iniciar el servidor de desarrollo:**
    ```bash
    php spark serve
    ```
    La aplicación estará disponible en `http://localhost:8080`.

## 📖 Uso de la Aplicación

1.  **Página Principal (`/` o `/categorias`)**: Al acceder, verás la lista de categorías almacenadas localmente. Si está vacía, puedes importarlas.
2.  **Importar Categorías**: Haz clic en el botón "Importar Categorías". Esto ejecutará una llamada AJAX al endpoint `/categorias/importar-ajax`, que traerá las categorías desde la API de Aleph y las guardará en tu base de datos.
3.  **Ver Registros de CMDB**: Haz clic en el nombre de una categoría para navegar a la vista `cmdb/{id}`. Esta página mostrará una tabla con todos los registros de la CMDB que pertenecen a esa categoría.
4.  **Exportar Registros**: En la vista de registros, un botón "Exportar" te permitirá descargar un archivo con todos los datos de esa vista.
5.  **Importar Registros**: Un botón "Importar" abrirá un formulario (o modal) para que puedas subir un archivo (`.csv`, `.xls`, `.xlsx`). Los datos del archivo serán procesados y enviados a la API de Aleph para crear o actualizar registros.

## 🗂️ Estructura del Proyecto

A continuación se describe el propósito de los archivos clave que proporcionaste:

-   `app/Controllers/CategoriaController.php`: Gestiona la lógica relacionada con las categorías: listarlas desde la base de datos local e importarlas desde la API.
-   `app/Controllers/CmdbController.php`: Controla las operaciones sobre los registros de la CMDB: visualización por categoría, exportación a archivo e importación desde archivo.
-   `app/Libraries/AlephAPI.php`: Una clase encapsulada (wrapper) que maneja toda la comunicación con la API externa de Aleph Manager. Centraliza los endpoints y la autenticación.
-   `app/Models/CategoryModel.php`: Modelo de CodeIgniter que interactúa con la tabla `categorias` de la base de datos. Incluye lógica para evitar la inserción de categorías duplicadas.
-   `app/Services/CmdbService.php`: (Referenciado pero no provisto) Contiene la lógica de negocio para procesar la exportación e importación de archivos, separando esta responsabilidad del controlador.
-   `app/Config/Routes.php`: Define las URLs de la aplicación y las asocia a los métodos de los controladores correspondientes.
-   `app/Helpers/text_helper.php`: (Contiene `slugify`) Un archivo de ayuda con funciones de utilidad. En este caso, para convertir texto a un formato amigable para URLs.

## 🗺️ Rutas Definidas (Endpoints)

| Método | URI                               | Controlador::Método                  | Descripción                                          |
| :----- | :-------------------------------- | :----------------------------------- | :--------------------------------------------------- |
| `GET`  | `/` ó `/categorias`               | `CategoriaController::index`         | Muestra la lista de categorías.                      |
| `POST` | `/categorias/importar-ajax`       | `CategoriaController::importarCategoriasAjax`  | Importa las categorías desde la API vía AJAX.        |
| `GET`  | `/cmdb/(:num)`                    | `CmdbController::index/$1`           | Muestra los registros de una categoría específica.   |
| `GET`  | `/cmdb/exportar/(:num)`           | `CmdbController::exportar/$1`        | Inicia la exportación de registros para una categoría. |
| `GET`  | `/cmdb/importar-vista/(:num)`     | `CmdbController::importarVista/$1`   | Muestra el formulario para importar archivos.        |
| `POST` | `/cmdb/importar`                  | `CmdbController::importar`           | Procesa el archivo subido para la importación.       |
````