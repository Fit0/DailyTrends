# DailyTrends API 🚀

**DailyTrends** es un agregador de noticias de portada de alto rendimiento desarrollado en **Symfony 7.2** y **PHP 8.4**. El sistema automatiza la recolección de noticias de *El País* y *El Mundo* mediante web scraping y proporciona una interfaz API REST robusta para la gestión del contenido.

## 🏛️ 1. Arquitectura y Decisiones Técnicas

El proyecto ha sido diseñado bajo los estándares de **Arquitectura Limpia** y principios **SOLID** para garantizar su escalabilidad y mantenibilidad.

### Patrones de Diseño Utilizados

* **Patrón Strategy (Polimorfismo):** Implementado mediante `NewsScraperInterface`. Esto permite que el motor de importación sea independiente de las reglas de cada periódico. Gracias al `TaggedIterator` de Symfony, añadir un nuevo medio es "Plug & Play".
* **Patrón Repository:** Desacopla la lógica de persistencia de la lógica de negocio.
* **Data Transfer Objects (DTO):** Se utiliza `FeedInputDTO` para todas las operaciones de entrada, asegurando que la API nunca exponga directamente las entidades de la base de datos.

### Componentes Clave

* **Scraping Resiliente:** Los scrapers (`ScraperElPais`, `ScraperElMundo`) utilizan `DomCrawler` con manejo de errores detallado para evitar que fallos en un titular detengan toda la importación.
* **Manejo de Duplicados Eficiente:** En `FeedManager`, se implementa una estrategia de precarga de URLs en memoria para minimizar las consultas SQL (evitando el problema N+1).
* **Gestión Global de Errores:** Un `ApiExceptionListener` intercepta excepciones para devolver respuestas JSON estandarizadas.

---

## 🔄 2. Flujos Críticos del Sistema

### A. Flujo de Extracción (Scraping)

El comando no conoce los detalles de cada periódico, simplemente ejecuta el contrato definido en la interfaz.

### B. Validación de Datos (DTO Pipeline)

Ningún dato externo llega a la entidad sin antes ser filtrado y validado por la capa de aplicación.

---

## 🛠️ 3. Requisitos y Configuración

* **Entorno:** Docker con PHP 8.4, Nginx y MySQL.
* **Puerto local:** `8890`

### Instalación

1. **Clonar el proyecto:**
```bash
git clone https://github.com/Fit0/DailyTrends.git casfid-technical-test

```

2. **Levantar el entorno:**
```bash
docker-compose up -d

```

3. **Instalar dependencias:**
```bash
docker-compose exec server_casfid_technical_test composer install

```


4. **Configurar base de datos:**
```bash
docker-compose exec server_casfid_technical_test php bin/console doctrine:migrations:migrate

```



---

## 🛰️ 4. Documentación de la API (Endpoints)

La API y su documentación interactiva están centralizadas en el puerto **8890**.

### Swagger UI (Documentación Interactiva)

Para visualizar, probar los endpoints y revisar los esquemas de datos:
👉 **[http://localhost:8890/api/doc](http://localhost:8890/api/doc)**

### Resumen de Endpoints

| Método | Endpoint | Acción | Validación DTO |
| --- | --- | --- | --- |
| **GET** | `/api/feeds` | Lista todas las noticias. | No |
| **GET** | `/api/feeds/{id}` | Detalle de una noticia. | No |
| **POST** | `/api/feeds` | Creación manual. | **Sí** |
| **PUT** | `/api/feeds/{id}` | Actualización completa. | **Sí** |
| **DELETE** | `/api/feeds/{id}` | Eliminación física. | No |

---

## 🤖 5. Comando de Importación (CLI)

Para ejecutar el scraping automático y agregar noticias en la base de datos:

```bash
docker-compose exec server_casfid_technical_test php bin/console app:import-news

```

---

## 🧪 6. Estrategia de Testing

Se ha implementado una suite de pruebas con **PHPUnit** cubriendo el 100% de la lógica crítica:

* **Unit Tests (`FeedManagerTest`):** Valida la lógica de negocio y la detección de duplicados mediante Mocks.
* **Integration Tests (`ScraperElPaisTest`):** Verifica la extracción contra fixtures HTML locales, evitando la fragilidad de depender de la red externa.
* **Functional Tests (`FeedControllerTest`):** Pruebas E2E del ciclo de vida CRUD y respuestas del `ApiExceptionListener`.

**Ejecución de tests:**

**Configurar base de datos:**
```bash
docker-compose exec server_casfid_technical_test php bin/console doctrine:database:create --env=test
docker-compose exec server_casfid_technical_test php bin/console doctrine:migrations:migrate --env=test

```

```bash
docker-compose exec server_casfid_technical_test php bin/phpunit

```
