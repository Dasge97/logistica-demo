# SPEC - Logistica y Tarifas

## 1. Resumen ejecutivo

Logistica y Tarifas es una aplicacion monolitica en Symfony para gestionar un motor de decision logistica en tiempo real.

El sistema debe determinar, de forma coherente y explicable:

- que opciones de entrega pueden ofrecerse para un pedido;
- cuanto debe pagar el cliente;
- cuanto costara ejecutar el servicio;
- que vehiculo es compatible y cual conviene seleccionar;
- que snapshot inmutable debe guardarse al confirmar el pedido.

No es un simple configurador de tarifas. Es un producto operativo con motor de decision, panel administrativo editable, simulador visual e historico auditable.

## 2. Objetivo de la v1

Construir una primera version funcional, desplegable y usable por negocio unico, capaz de:

- crear pedidos manualmente;
- introducir distancia de forma manual;
- calcular peso y volumen desde lineas de pedido;
- decidir que servicios son viables;
- calcular el precio cliente por reglas comerciales;
- calcular el coste logistico por vehiculo;
- elegir el vehiculo optimo;
- congelar un snapshot inmutable al confirmar;
- permitir que toda la configuracion relevante se edite desde interfaz.

## 3. Restricciones y decisiones de contexto

- Proyecto nuevo e independiente.
- Monolito Symfony desplegable.
- Base de datos PostgreSQL.
- Single-company en la v1.
- Todo el lenguaje funcional del proyecto en espanol.
- Distancia manual en la v1.
- Sin integraciones externas de mapas en la v1.
- Estados simples de pedido: `borrador`, `confirmado`, `cancelado`.

## 4. Principios funcionales obligatorios

1. Nunca ofrecer servicios no viables.
2. La viabilidad se evalua antes que el precio.
3. El precio cliente no depende del vehiculo.
4. El coste logistico si depende del vehiculo.
5. La logica comercial y la logica operativa deben estar separadas.
6. Todas las decisiones deben ser deterministas.
7. Todas las decisiones deben ser explicables.
8. El snapshot confirmado no se recalcula ni se modifica.
9. La configuracion debe ser editable desde pantalla.

## 5. Alcance funcional de la v1

### Incluye

- autenticacion basica de backoffice;
- CRUD de tipos de cliente;
- CRUD de niveles de servicio;
- CRUD de tipos de vehiculo;
- CRUD de reglas de disponibilidad;
- CRUD de tarifas cliente;
- CRUD de tarifas transportista;
- gestion de pedidos manuales;
- lineas de pedido con peso y volumen;
- calculo de metricas de pedido;
- simulador logistico visual;
- confirmacion de pedido con snapshot;
- consulta de snapshots historicos.

### No incluye

- geocoding;
- calculo automatico de distancia;
- asignacion de repartidores;
- optimizacion de rutas;
- multialmacen;
- multiempresa real;
- API publica separada;
- integraciones con terceros.

## 6. Stack tecnico

- Symfony
- Doctrine ORM
- Doctrine Migrations
- Twig
- Symfony Forms
- Symfony Security
- Stimulus
- PostgreSQL
- Docker Compose

## 7. Arquitectura

### 7.1 Estilo de aplicacion

Monolito modular por dominios con separacion por capas:

- `Domain`: entidades, enums, value objects y reglas de dominio.
- `Application`: casos de uso y servicios de orquestacion.
- `Infrastructure`: controllers, formularios, persistencia Doctrine, comandos y adaptadores.

### 7.2 Estructura de carpetas propuesta

```text
src/
  Core/
    Seguridad/
    Usuario/
    Configuracion/
  Modulos/
    Catalogos/
      Domain/
      Application/
      Infrastructure/
    Pedidos/
      Domain/
      Application/
      Infrastructure/
    Tarifas/
      Domain/
      Application/
      Infrastructure/
    Operaciones/
      Domain/
      Application/
      Infrastructure/
    Decisiones/
      Domain/
      Application/
      Infrastructure/
    Simulador/
      Application/
      Infrastructure/
  Shared/
    Domain/
    Infrastructure/
templates/
```

## 8. Modelo de dominio

### 8.1 Pedido

Agregado principal del sistema.

Campos previstos:

- `id`
- `referencia`
- `estado`
- `nombreCliente`
- `telefonoCliente`
- `tipoCliente`
- `distanciaKm`
- `pesoTotalKg`
- `volumenTotalM3`
- `servicioElegido`
- `vehiculoElegido`
- `precioCliente`
- `costeLogistico`
- `margen`
- `snapshotTarificacion`
- `createdAt`
- `updatedAt`

Reglas:

- solo puede confirmarse si existe una decision valida;
- el snapshot se asocia al confirmar;
- un pedido confirmado no debe recalcularse automaticamente.

### 8.2 LineaPedido

Representa cada elemento de carga del pedido.

Campos previstos:

- `id`
- `pedido`
- `descripcion`
- `cantidad`
- `pesoUnitarioKg`
- `volumenUnitarioM3`
- `subtotalPesoKg`
- `subtotalVolumenM3`

Reglas:

- cantidad positiva;
- peso y volumen no negativos;
- subtotales derivados.

### 8.3 TipoCliente

Catalogo comercial.

Campos previstos:

- `id`
- `nombre`
- `codigo`
- `activo`

Fixtures base:

- Particular
- Profesional

### 8.4 NivelServicioEntrega

Catalogo de servicios ofrecibles.

Campos previstos:

- `id`
- `nombre`
- `codigo`
- `activo`
- `ordenVisual`

Fixtures base:

- Express
- Standard
- Programado

### 8.5 TipoVehiculo

Catalogo logistico.

Campos previstos:

- `id`
- `nombre`
- `codigo`
- `pesoMaximoKg`
- `volumenMaximoM3`
- `activo`

Fixtures base:

- Patinete
- Moto
- Coche
- Furgoneta

### 8.6 ReglaDisponibilidadServicio

Regla operativa para determinar si un nivel de servicio puede mostrarse.

Campos previstos:

- `id`
- `nivelServicioEntrega`
- `distanciaMaximaKm`
- `pesoMaximoKg`
- `volumenMaximoM3`
- `activa`

Reglas:

- se evalua antes de cualquier calculo comercial;
- si no existe regla activa aplicable, el servicio no se ofrece.

### 8.7 ReglaTarifaCliente

Regla comercial de precio al cliente.

Campos previstos:

- `id`
- `tipoCliente`
- `nivelServicioEntrega`
- `distanciaDesdeKm`
- `distanciaHastaKm`
- `precioCliente`
- `activa`

Reglas:

- solo depende de tipo de cliente, servicio y distancia;
- no depende de peso, volumen ni vehiculo.

### 8.8 ReglaTarifaTransportista

Regla logistica de coste real.

Campos previstos:

- `id`
- `tipoVehiculo`
- `nivelServicioEntrega`
- `precioBase`
- `distanciaIncluidaKm`
- `precioKmExtra`
- `activa`

Formula:

`coste = precioBase + max(0, distanciaKm - distanciaIncluidaKm) * precioKmExtra`

### 8.9 SnapshotTarificacionPedido

Fotografia inmutable del calculo confirmado.

Campos previstos:

- `id`
- `pedido`
- `nombreServicio`
- `nombreVehiculo`
- `distanciaKm`
- `pesoTotalKg`
- `volumenTotalM3`
- `precioCliente`
- `costeLogistico`
- `margen`
- `explicacionJson`
- `createdAt`

Debe conservar tanto datos estructurados como una explicacion legible del resultado.

## 9. Casos de uso principales

### 9.1 Crear pedido

- crear un pedido en `borrador`;
- añadir lineas;
- introducir distancia manual.

### 9.2 Calcular metricas de pedido

- sumar peso total;
- sumar volumen total.

### 9.3 Resolver opciones de entrega

- evaluar todos los niveles de servicio;
- descartar los no viables;
- calcular precio cliente para los viables;
- devolver opciones ofertables con explicacion.

### 9.4 Resolver vehiculo para servicio elegido

- filtrar vehiculos compatibles por peso y volumen;
- calcular coste por cada vehiculo compatible;
- elegir el mas conveniente.

### 9.5 Confirmar pedido

- fijar servicio elegido;
- fijar vehiculo elegido;
- fijar precio cliente, coste y margen;
- guardar snapshot;
- cambiar estado a `confirmado`.

### 9.6 Simular escenario

- introducir manualmente datos del pedido;
- obtener servicios posibles;
- visualizar precios, costes, vehiculos y motivos de descarte.

## 10. Servicios de aplicacion previstos

- `CalculadoraMetricasPedido`
- `ServicioDisponibilidadEntrega`
- `ServicioTarificacionCliente`
- `ValidadorVehiculos`
- `ServicioCosteTransportista`
- `ResolutorOpcionesEntrega`
- `SelectorVehiculoOptimo`
- `ServicioSnapshotPedido`
- `ConstructorExplicacionDecision`

## 11. Flujo funcional detallado

1. El usuario crea un pedido.
2. Añade lineas con peso y volumen unitario.
3. Informa la distancia manual.
4. El sistema calcula peso total y volumen total.
5. El sistema evalua todos los niveles de servicio.
6. El sistema descarta los no viables.
7. El sistema calcula el precio cliente de los servicios viables.
8. El usuario visualiza las opciones reales.
9. El usuario selecciona un servicio.
10. El sistema busca vehiculos compatibles.
11. El sistema calcula el coste logistico por vehiculo.
12. El sistema selecciona el vehiculo optimo.
13. El usuario confirma el pedido.
14. El sistema guarda snapshot inmutable.

## 12. Pantallas de la v1

- Inicio
- Acceso
- Pedidos
- Nuevo pedido
- Editar pedido
- Detalle de pedido
- Simulador logistico
- Tipos de cliente
- Niveles de servicio
- Tipos de vehiculo
- Reglas de disponibilidad
- Tarifas cliente
- Tarifas transportista
- Snapshots de pedidos

## 13. Fixtures iniciales

### Niveles de servicio

- Express
- Standard
- Programado

### Vehiculos

- Patinete
- Moto
- Coche
- Furgoneta

### Reglas de disponibilidad

- Express: max 5 km
- Standard: max 15 km
- Programado: max 40 km

### Tarifas cliente

- Express 0-5 km -> 14 EUR
- Standard 0-10 km -> 8 EUR
- Programado 0-10 km -> 6 EUR

### Tarifas transportista

- Moto + Express -> base 8.5 EUR, 6 km incluidos
- Coche + Express -> base 12 EUR, 10 km incluidos

## 14. Requisitos de calidad

- lenguaje funcional en espanol;
- servicios pequenos y con responsabilidad unica;
- controladores finos;
- logica de negocio fuera de Twig y fuera de controllers;
- snapshot inmutable;
- decisiones auditables;
- interfaz operativa y pensada para prueba visual real.

## 15. Despliegue esperado

La v1 debe poder desplegarse como:

- aplicacion Symfony;
- base de datos PostgreSQL;
- contenedores Docker Compose;
- proxy inverso del servidor si se requiere.

## 16. Roadmap de alto nivel

1. Base tecnica del proyecto.
2. Seguridad, layout y navegacion.
3. Catalogos.
4. Pedidos y lineas.
5. Reglas de disponibilidad y tarifas.
6. Motor de decision.
7. Simulador visual.
8. Confirmacion y snapshot.
9. Fixtures demo.
10. Tests funcionales y de dominio.
11. Docker y despliegue.
