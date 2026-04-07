# TASKS - V1 Logistica y Tarifas

## Criterios de ejecucion

- Cada tarea debe dejar el proyecto en un estado coherente.
- No avanzar a la siguiente tarea sin validar la anterior.
- Mantener el lenguaje funcional del producto en espanol.
- Priorizar primero base tecnica, despues dominio, despues UI operativa.

## Tarea 1 - Base tecnica del proyecto

Objetivo:

- crear la aplicacion Symfony;
- configurar dependencias base;
- preparar estructura modular inicial;
- dejar Docker Compose y configuracion de entorno listos para trabajar.

Entregables:

- proyecto Symfony inicializado;
- Composer con Doctrine, Twig, Forms, Security, Stimulus y Migrations;
- estructura `src/Core`, `src/Modulos`, `src/Shared`;
- configuracion inicial de base de datos;
- Docker Compose operativo.

## Tarea 2 - Seguridad, layout y navegacion base

Objetivo:

- crear autenticacion de backoffice;
- layout principal;
- menu lateral o superior;
- pagina de inicio y acceso.

Entregables:

- login funcional;
- usuario inicial configurable o fixture;
- plantilla base Twig;
- navegacion comun del sistema.

## Tarea 3 - Shared y piezas transversales

Objetivo:

- crear piezas compartidas que usara todo el proyecto.

Entregables:

- `Dinero` como value object embebible;
- `TimestampableTrait`;
- utilidades de validacion y formateo comunes;
- convenciones base para enums y helpers de interfaz.

## Tarea 4 - Modulo Catalogos

Objetivo:

- implementar catalogos de negocio editables.

Entregables:

- entidad `TipoCliente`;
- entidad `NivelServicioEntrega`;
- entidad `TipoVehiculo`;
- migraciones;
- repositorios Doctrine;
- formularios Symfony;
- controladores CRUD;
- vistas Twig.

## Tarea 5 - Modulo Pedidos

Objetivo:

- implementar el agregado principal del sistema.

Entregables:

- entidad `Pedido`;
- entidad `LineaPedido`;
- enum de estado de pedido;
- relaciones Doctrine;
- migraciones;
- CRUD base de pedidos;
- edicion de lineas desde interfaz.

## Tarea 6 - Calculo de metricas del pedido

Objetivo:

- calcular peso total y volumen total a partir de las lineas.

Entregables:

- `CalculadoraMetricasPedido`;
- actualizacion coherente de `Pedido`;
- cobertura de casos basicos;
- reflejo visual en detalle de pedido.

## Tarea 7 - Reglas de disponibilidad operativa

Objetivo:

- modelar y gestionar la viabilidad de servicios.

Entregables:

- entidad `ReglaDisponibilidadServicio`;
- migraciones;
- CRUD de reglas;
- `ServicioDisponibilidadEntrega`.

## Tarea 8 - Reglas de tarifa cliente

Objetivo:

- modelar y gestionar el precio comercial.

Entregables:

- entidad `ReglaTarifaCliente`;
- migraciones;
- CRUD de reglas;
- `ServicioTarificacionCliente`.

## Tarea 9 - Reglas de tarifa transportista

Objetivo:

- modelar y gestionar el coste logistico.

Entregables:

- entidad `ReglaTarifaTransportista`;
- migraciones;
- CRUD de reglas;
- `ServicioCosteTransportista`.

## Tarea 10 - Resolver de opciones de entrega

Objetivo:

- construir el motor que ofrece servicios reales al cliente.

Entregables:

- `ValidadorVehiculos`;
- `ResolutorOpcionesEntrega`;
- `SelectorVehiculoOptimo`;
- objetos resultado para opcion, descarte y explicacion;
- logica determinista y trazable.

## Tarea 11 - Simulador logistico visual

Objetivo:

- permitir probar reglas y escenarios desde UI.

Entregables:

- pantalla `Simulador logistico`;
- formulario de entrada manual;
- listado de servicios viables y descartados;
- listado de vehiculos compatibles;
- coste, precio y margen por escenario;
- explicacion legible de cada decision.

## Tarea 12 - Seleccion de servicio y confirmacion de pedido

Objetivo:

- llevar la logica del simulador al flujo real de pedido.

Entregables:

- seleccion de servicio desde pedido;
- resolucion de vehiculo;
- confirmacion del pedido;
- bloqueo de cambios incompatibles tras confirmar.

## Tarea 13 - Snapshot inmutable y trazabilidad

Objetivo:

- guardar la fotografia final del calculo confirmado.

Entregables:

- entidad `SnapshotTarificacionPedido`;
- migraciones;
- `ServicioSnapshotPedido`;
- persistencia de datos estructurados + JSON explicativo;
- pantalla de consulta de snapshots.

## Tarea 14 - Dashboard y experiencia operativa

Objetivo:

- mejorar la legibilidad y operativa del producto.

Entregables:

- pantalla de inicio con accesos y metricas basicas;
- mensajes de estado claros;
- mejoras de navegacion;
- mejora del detalle de pedido y del simulador.

## Tarea 15 - Fixtures demo

Objetivo:

- dejar el sistema listo para pruebas funcionales reales.

Entregables:

- tipos de cliente demo;
- servicios demo;
- vehiculos demo;
- reglas demo;
- pedidos demo si aportan valor.

## Tarea 16 - Tests de dominio y funcionales

Objetivo:

- validar el comportamiento principal de la v1.

Entregables:

- tests de calculo de metricas;
- tests de viabilidad;
- tests de tarificacion cliente;
- tests de coste transportista;
- tests del resolver;
- tests funcionales basicos de interfaz.

## Tarea 17 - Docker y despliegue

Objetivo:

- dejar la aplicacion lista para desplegar.

Entregables:

- Dockerfile;
- `docker-compose.yml` o equivalente;
- configuracion de produccion basica;
- instrucciones de despliegue;
- checklist operativa.

## Orden recomendado de ejecucion

1. Tarea 1
2. Tarea 2
3. Tarea 3
4. Tarea 4
5. Tarea 5
6. Tarea 6
7. Tarea 7
8. Tarea 8
9. Tarea 9
10. Tarea 10
11. Tarea 11
12. Tarea 12
13. Tarea 13
14. Tarea 14
15. Tarea 15
16. Tarea 16
17. Tarea 17
