# CODEX INTEGRATION CONTEXT

## 1. Proposito de este documento

Este documento existe para que un agente como Codex pueda entender el sistema de logistica y tarifas implementado en este proyecto y migrarlo o integrarlo dentro de otro sistema real sin depender de la arquitectura actual de este repositorio.

La idea central es separar:

- el dominio y las reglas de negocio, que deben mantenerse;
- de la arquitectura tecnica concreta de este proyecto, que puede cambiar por completo en el sistema destino.

Por tanto:

- este documento SI define con mucho detalle el comportamiento del modulo;
- este documento NO obliga a mantener la misma estructura de carpetas, servicios, controllers, formularios o framework en el sistema destino.

## 2. Que es este sistema

Este sistema no es un simple configurador de tarifas.

Es un motor de decision logistica en tiempo real que, para un pedido individual, debe ser capaz de determinar:

1. si un servicio de entrega puede ofrecerse o no;
2. cuanto pagaria el cliente;
3. que vehiculos podrian ejecutar realmente el servicio;
4. cual es el vehiculo mas conveniente para la empresa;
5. cual seria el snapshot final de la decision si el pedido se confirmara.

## 3. Objetivo funcional del modulo

El objetivo del modulo es responder a esta pregunta:

> Dado un pedido con una distancia, un peso y un volumen, que servicios de entrega son ofertables al cliente y como se ejecutarian en la realidad.

El sistema debe impedir que aparezcan opciones que:

- no sean operativamente viables;
- no tengan precio cliente definido;
- no tengan ningun vehiculo ejecutable;
- o no sean sostenibles economicamente segun las reglas cargadas.

## 4. Filosofia del modulo

Las premisas funcionales que hay que preservar en cualquier integracion son estas:

1. Nunca mostrar al cliente una opcion no ejecutable.
2. La viabilidad se evalua antes que el precio.
3. El precio cliente y el coste interno son dos cosas diferentes.
4. El cliente ve servicios y precios; el sistema resuelve internamente el vehiculo.
5. El sistema debe ser determinista.
6. El sistema debe poder justificar por que una opcion fue aceptada o descartada.
7. El sistema trabaja, en esta fase, sobre pedidos individuales; no sobre optimizacion de flota multi-pedido.

## 5. Alcance conceptual actual

Este motor modela decision logistica por pedido aislado.

Eso significa:

- no planifica rutas multi-parada;
- no consolida varios pedidos en una sola ruta;
- no hace optimizacion de flota global;
- no evalua capacidad restante de un vehiculo ya cargado con otros pedidos;
- no considera calendarios complejos ni ventanas horarias avanzadas.

Lo que si hace:

- evaluar un pedido individual;
- decidir que servicios son ofertables;
- decidir con que vehiculo se ejecutaria;
- y producir un snapshot coherente de esa decision.

## 6. Vocabulario canonico del dominio

Estos conceptos deben mantenerse aunque los nombres tecnicos cambien en el sistema destino.

### 6.1 Pedido

Unidad principal sobre la que se toma la decision logistica.

Debe contener, como minimo:

- tipo de cliente;
- distancia;
- peso total;
- volumen total.

Opcionalmente puede contener:

- lineas de pedido;
- direccion origen y destino;
- servicio elegido;
- vehiculo elegido;
- snapshot confirmado.

### 6.2 Tipo de cliente

Clasifica el perfil comercial del cliente.

Ejemplos:

- Particular
- Profesional

Se usa en la capa de tarifa cliente.

### 6.3 Nivel de servicio

Representa la modalidad comercial de entrega.

Ejemplos:

- Express
- Standard
- Programado

### 6.4 Vehiculo

Representa el recurso logistico con el que podria ejecutarse el pedido.

Ejemplos:

- Patinete
- Moto
- Coche
- Furgoneta

### 6.5 Regla de disponibilidad del servicio

Regla global de servicio.

Responde a:

> Este servicio puede existir para este pedido?

### 6.6 Regla de tarifa cliente

Regla comercial.

Responde a:

> Cuanto veria o pagaria el cliente si se le ofrece este servicio?

### 6.7 Regla de tarifa transportista

Regla de coste interno por vehiculo.

Responde a:

> Para este vehiculo y este servicio, existe una tarifa aplicable a este pedido y cuanto costaria ejecutarlo?

### 6.8 Snapshot

Fotografia inmutable de la decision final ya resuelta.

Responde a:

> Que se decidio exactamente en el momento de confirmar?

## 7. Capas logicas del sistema

Aunque la arquitectura tecnica del proyecto destino pueda ser distinta, la logica del motor debe seguir estas capas.

### 7.1 Capa de entrada del pedido

Datos minimos necesarios para resolver:

- tipo de cliente;
- distancia real del pedido;
- peso total del pedido;
- volumen total del pedido.

En este proyecto actual la distancia se introduce manualmente.
En otro sistema puede venir de mapas, de geocoding o de otra fuente.

La fuente de distancia puede cambiar.
La logica del motor no.

### 7.2 Capa de viabilidad del servicio

Para cada nivel de servicio activo se evalua si el pedido entra dentro de los maximos admitidos por el servicio.

La idea es:

- un servicio puede ser inviable incluso antes de pensar en vehiculos;
- si falla aqui, no debe mostrarse al cliente.

### 7.3 Capa de precio cliente

Si el servicio pasa la viabilidad, se busca una regla de tarifa cliente aplicable.

Si no existe tarifa cliente:

- el servicio no se puede ofertar;
- aunque fuera viable operativamente.

### 7.4 Capa de capacidad de vehiculo

Se revisa que el pedido pueda ser cargado por el vehiculo segun la capacidad fisica del tipo de vehiculo.

Esto se evalua con:

- peso maximo del vehiculo;
- volumen maximo del vehiculo.

### 7.5 Capa de aplicabilidad de tarifa transportista

Una vez un vehiculo cabe fisicamente, no basta con eso.

Ahora tambien debe existir una o varias reglas transportista aplicables segun:

- distancia minima y maxima;
- peso minimo y maximo;
- volumen minimo y maximo.

Esto permite que la regla de coste del vehiculo no sea global, sino contextual.

### 7.6 Capa de coste interno

Cuando hay varias reglas transportista aplicables para el mismo vehiculo y servicio:

- se calculan todas;
- se elige la de menor coste para la empresa.

### 7.7 Capa de sostenibilidad economica

Si el coste interno del mejor vehiculo supera el precio cliente:

- esa opcion no debe mostrarse al cliente.

Conclusión:

- una opcion no se muestra si da perdida con las reglas definidas.

### 7.8 Capa de seleccion del vehiculo

Entre los vehiculos viables y sostenibles:

- el sistema elige el vehiculo mas conveniente para la empresa;
- en esta fase, eso significa el de menor coste interno.

### 7.9 Capa de snapshot

Cuando ya se ha elegido el servicio y el vehiculo final:

- se genera un snapshot inmutable con la decision final.

## 8. Regla general del flujo

El flujo canonico del motor, que Codex debe preservar al migrar, es este:

1. Recibir datos del pedido.
2. Calcular o recibir distancia, peso y volumen.
3. Evaluar todos los niveles de servicio activos.
4. Descartar servicios no viables globalmente.
5. Para los viables, resolver tarifa cliente.
6. Descartar servicios sin precio cliente.
7. Para cada servicio superviviente, evaluar vehiculos activos.
8. Descartar vehiculos sin capacidad fisica.
9. Buscar reglas transportista aplicables por rango.
10. Si varias reglas aplican, calcular todas.
11. Quedarse con la regla transportista de menor coste para ese vehiculo.
12. Comparar vehiculos validos del servicio.
13. Elegir el vehiculo mas conveniente para la empresa.
14. Si el coste del mejor vehiculo supera el precio cliente, descartar el servicio.
15. Mostrar al cliente solo los servicios supervivientes.
16. Tras eleccion o simulacion final, construir snapshot.

## 9. Datos minimos requeridos por el motor

El motor necesita como entrada, como minimo:

- `tipoClienteId` o equivalente;
- `distanciaKm`;
- `pesoTotalGramos`;
- `volumenTotalCm3`.

No debe depender estrictamente de una entidad `Pedido` concreta.
Si el sistema destino no tiene el mismo modelo, Codex debe adaptar una entrada equivalente.

## 10. Unidades canonicas

Estas unidades deben mantenerse para no introducir errores:

- distancia: `km`
- peso: `gramos`
- volumen: `cm3`
- importes: `centimos`

Si el sistema destino usa otras unidades:

- Codex debe convertirlas antes de entrar al motor;
- no debe mezclar unidades dentro del mismo calculo.

## 11. Entidades o conceptos que el sistema destino necesita

Aunque los nombres pueden cambiar, el sistema destino necesita equivalentes de estos conceptos.

### 11.1 TipoCliente

Campos minimos:

- identificador
- nombre
- codigo
- activo

### 11.2 NivelServicioEntrega

Campos minimos:

- identificador
- nombre
- codigo
- activo
- orden visual opcional

### 11.3 TipoVehiculo

Campos minimos:

- identificador
- nombre
- codigo
- pesoMaximoGramos
- volumenMaximoCm3
- activo

### 11.4 ReglaDisponibilidadServicio

Campos minimos:

- nivelServicioEntrega
- distanciaMaximaKm
- pesoMaximoGramos
- volumenMaximoCm3
- activa

### 11.5 ReglaTarifaCliente

Campos minimos:

- tipoCliente
- nivelServicioEntrega
- distanciaDesdeKm
- distanciaHastaKm
- precioClienteCentimos
- activa

Nota importante:

En la fase actual no incluimos peso o volumen en tarifa cliente.
La tarifa cliente sigue siendo una capa comercial basada en cliente, servicio y distancia.

### 11.6 ReglaTarifaTransportista

Campos minimos y definitivos para esta fase:

- tipoVehiculo
- nivelServicioEntrega
- distanciaMinimaKm
- distanciaMaximaKm
- pesoMinimoGramos
- pesoMaximoGramos
- volumenMinimoCm3
- volumenMaximoCm3
- precioBaseCentimos
- distanciaIncluidaKm
- precioKmExtraCentimos
- activa

## 12. Semantica exacta de ReglaTarifaTransportista

Esta es la parte mas importante para la migracion.

### 12.1 Para que sirve

No solo calcula coste.

Tambien define si la tarifa es aplicable a ese pedido.

### 12.2 Que significa cada campo

- `distanciaMinimaKm`: distancia minima para que la regla entre en juego
- `distanciaMaximaKm`: distancia maxima para que la regla entre en juego
- `pesoMinimoGramos`: peso minimo del pedido para que la regla aplique
- `pesoMaximoGramos`: peso maximo del pedido para que la regla aplique
- `volumenMinimoCm3`: volumen minimo del pedido para que la regla aplique
- `volumenMaximoCm3`: volumen maximo del pedido para que la regla aplique
- `precioBaseCentimos`: coste base de la operacion si la regla aplica
- `distanciaIncluidaKm`: km incluidos antes de activar recargo por km extra
- `precioKmExtraCentimos`: coste adicional por cada km que exceda `distanciaIncluidaKm`

### 12.3 Regla de aplicabilidad

Una regla transportista aplica si y solo si:

- el vehiculo y el servicio coinciden;
- esta activa;
- la distancia del pedido esta entre `distanciaMinimaKm` y `distanciaMaximaKm`;
- el peso total esta entre `pesoMinimoGramos` y `pesoMaximoGramos`;
- el volumen total esta entre `volumenMinimoCm3` y `volumenMaximoCm3`.

### 12.4 Formula de coste

Una vez la regla aplica:

`coste = precioBaseCentimos + max(0, distanciaRealKm - distanciaIncluidaKm) * precioKmExtraCentimos`

Importante:

- `distanciaMin/Max` definen la ventana de aplicabilidad;
- `distanciaIncluidaKm` define desde que punto se cobra recargo.

No son el mismo concepto.

### 12.5 Solapes

Los solapes entre reglas transportista estan permitidos.

Esto es deliberado.

Razones:

- el sistema puede evolucionar a una logistica mas compleja;
- no queremos cerrar el modelo a reglas estrictamente excluyentes;
- un mismo vehiculo y servicio pueden tener varias reglas candidatas.

### 12.6 Como se resuelven los solapes

Si varias reglas transportista aplican al mismo vehiculo y servicio:

1. se calculan todas;
2. se compara el coste final de cada una;
3. gana la regla de menor coste para la empresa.

No se usa prioridad manual.
No se usa la regla mas especifica.
No se bloquea la configuracion.

La regla de seleccion es:

> elegir la regla transportista aplicable que deje el menor coste interno.

## 13. Viabilidad economica final

El sistema asume esta politica:

- si una opcion da perdida, no debe mostrarse al cliente.

Traduccion exacta:

si el mejor coste interno resoluble para un servicio es mayor que el precio cliente,
entonces ese servicio se descarta.

Esto significa que:

- el sistema evita ofertar opciones no sostenibles;
- cara al cliente, solo existen opciones viables y sostenibles.

## 14. Diferencia entre servicio, vehiculo y tarifa transportista

Codex debe respetar esta separacion al migrar.

### 14.1 Servicio

El servicio define limites globales de oferta.

Ejemplo:

- Express no debe ofrecerse por encima de cierta distancia o carga.

### 14.2 Vehiculo

El vehiculo define capacidad fisica maxima.

Ejemplo:

- una Moto no puede cargar mas de cierto peso o volumen.

### 14.3 Tarifa transportista

La tarifa transportista define ventana operativa/economica de aplicabilidad y coste.

Ejemplo:

- una Moto puede caber fisicamente, pero su tarifa solo aplica en ciertos rangos de distancia, peso y volumen.

## 15. Algoritmo canonico de resolucion

Codex debe implementar un flujo equivalente a este, aunque la arquitectura del sistema destino sea diferente.

### Pseudocodigo funcional

```text
entrada: tipoCliente, distanciaKm, pesoTotalGramos, volumenTotalCm3

opciones = []
descartes = []

para cada servicio activo:
    si no cumple reglas de disponibilidad del servicio:
        descartar servicio
        continuar

    precioCliente = resolver tarifa cliente
    si no existe precioCliente:
        descartar servicio
        continuar

    vehiculosValidos = []

    para cada vehiculo activo:
        si el vehiculo no soporta fisicamente el pedido:
            continuar

        reglasAplicables = buscar reglas transportista aplicables
        si no hay reglasAplicables:
            continuar

        calcular coste de cada regla aplicable
        reglaElegida = regla con menor coste

        anadir vehiculo con su coste y regla elegida

    si no hay vehiculos validos:
        descartar servicio
        continuar

    elegir vehiculo de menor coste

    si costeVehiculoElegido > precioCliente:
        descartar servicio
        continuar

    anadir servicio ofertable con su precio y vehiculo elegido

devolver opciones y descartes
```

## 16. Que debe ver el cliente y que no

Cara al cliente o a la capa comercial visible:

- debe ver servicios ofertables;
- debe ver el precio cliente;
- no debe ver reglas internas ambiguas;
- no debe ver opciones no sostenibles.

En una capa operativa o de simulacion interna:

- si puede verse que vehiculos fueron barajados;
- puede verse cual se selecciono automaticamente;
- puede verse el coste interno;
- puede generarse un snapshot completo.

## 17. Snapshot final

Cuando una decision ya esta cerrada, el snapshot debe congelar como minimo:

- servicio elegido;
- vehiculo elegido;
- precio cliente;
- coste interno de ejecucion;
- distancia;
- peso total;
- volumen total;
- explicacion de la decision;
- si es posible, la regla transportista elegida o al menos sus datos relevantes.

Este snapshot no debe recalcularse automaticamente si luego cambian las reglas.

## 18. Que puede cambiar y que no al migrar

### 18.1 Puede cambiar

- framework;
- estructura de carpetas;
- nombres de clases;
- forma de persistir;
- frontend;
- controllers;
- servicios de aplicacion;
- DTOs concretos;
- forma de invocar el motor.

### 18.2 No deberia cambiar

- la separacion conceptual de capas;
- las unidades;
- el criterio de descarte;
- el criterio de coste transportista;
- el criterio de seleccion de regla transportista;
- el criterio de seleccion de vehiculo;
- la necesidad del snapshot final.

## 19. Errores frecuentes a evitar en la migracion

1. Mezclar tarifa cliente con tarifa transportista.
2. Calcular vehiculo antes de validar servicio.
3. Ignorar la capacidad fisica maxima del vehiculo.
4. Usar solo una regla transportista cuando puede haber varias aplicables.
5. Elegir una regla transportista arbitrariamente en vez de por menor coste.
6. Mostrar servicios que no tienen precio cliente.
7. Mostrar servicios que no tienen ningun vehiculo ejecutable.
8. Mostrar servicios cuyo coste interno supera el precio cliente.
9. Recalcular un snapshot ya confirmado.
10. Mezclar unidades distintas sin conversion explicita.

## 20. Casos de referencia para verificar integracion

Estos casos no deben copiarse literalmente si el sistema destino tiene otras reglas, pero sirven para entender el comportamiento esperado.

### Caso A

Entrada:

- Particular
- 3 km
- 3000 g
- 10000 cm3

Esperado:

- varios servicios ofertables;
- el precio cliente visible depende del servicio;
- el vehiculo se resuelve automaticamente.

### Caso B

Entrada:

- Particular
- 8 km
- 20000 g
- 110000 cm3

Esperado:

- Express descartado por distancia;
- Standard o Programado segun reglas;
- seleccion automatica de vehiculo;
- posible descarte de algun servicio por coste interno.

### Caso C

Entrada:

- Particular
- 12 km
- 30000 g
- 250000 cm3

Esperado:

- solo servicios mas amplios;
- vehiculos pequenos descartados;
- coche o furgoneta segun reglas.

### Caso D

Entrada:

- Profesional
- 3 km
- 3000 g
- 10000 cm3

Esperado:

- misma logica operativa que un particular;
- precio cliente distinto por tipo de cliente.

## 21. Preguntas que Codex debe hacerse al integrarlo en otro sistema

1. Cual es la entidad equivalente a Pedido?
2. De donde sale la distancia real?
3. El sistema destino ya calcula peso y volumen o hay que introducirlos?
4. Existen ya catalogos equivalentes de cliente, servicio y vehiculo?
5. Hay snapshot de pedidos o hay que crearlo?
6. En que momento del flujo del pedido debe resolverse este motor?
7. El sistema destino permite editar reglas desde UI o hay que crear esa pantalla?
8. El sistema destino necesita exponer esto via API, backoffice o ambos?

## 22. Resumen ejecutivo para Codex

Si Codex solo pudiera leer una seccion, esta seria la esencial:

- El modulo decide servicios de entrega para un pedido individual.
- Primero valida servicio global.
- Luego exige precio cliente.
- Luego filtra vehiculos por capacidad fisica.
- Luego filtra tarifas transportista por rango de distancia, peso y volumen.
- Si varias tarifas transportista aplican al mismo vehiculo, gana la de menor coste.
- Si varios vehiculos son posibles, gana el de menor coste.
- Si el mejor coste supera el precio cliente, el servicio se descarta.
- Solo se muestran al cliente servicios viables y sostenibles.
- El resultado final debe poder congelarse en un snapshot inmutable.

## 23. Recomendacion final para la migracion

Codex debe considerar este proyecto como una referencia de dominio y comportamiento, no como una referencia obligatoria de arquitectura.

La prioridad al integrar en otro sistema debe ser:

1. preservar reglas de negocio;
2. preservar el orden del flujo de decision;
3. preservar unidades y criterios de seleccion;
4. adaptar la implementacion tecnica al sistema destino.

Si la arquitectura del sistema destino difiere por completo, eso no invalida este documento.
Lo importante es que el comportamiento del motor siga siendo equivalente.
