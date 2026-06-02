### Problemas en la sincronizacion de pedido -> Facturas

1. En nuestro clientes tenemos el dato reference_id (ID + idClienteJazz),
   El usuario de nuestra base ID11561,

{
"dni": "18811647",
"name": "TORRICO DONATO",
"reference_id": ID11561
}

no se encuentra con ese ID dentro de Jazz, dentro de clientes de Jazz es el idCliente = 30425 y con otro atributo Numero = 17271

2. Segunda parte donde se deben agregar los productos del pedida ya creado, logre respectar la estructura de datos pero la api me retorna lo siguente:

{
"Code": 500,
"Message": "Error al agregar el articulo: : Object reference not set to an instance of an object."
}

Estructura enviada

{
"nroInterno": 388537,
"idProducto": 27,
"cantidad": 1,
"precio": 2,
"descuento": 0,
"unidad": 0,
"unidad1": 0,
"bultos": 0,
"despacho": "string",
"comision": 0,
"idPresupuestos": 0
}

<!-- Pendientes -->

1. Pedidos a jazz.
   Los productos que no estan en jazz usar codigo generico, ver si se puede mantener la descripcion del producto de cuaderno y el precio.
   Usar llamada que me paso emi de ejemplo.
2. Dejar registrado de donde es el pedido, si es mostrador, por ejemplo.
3. Poder agregar productos despues de haber enviado a jazz.
4. Pasar de cotizacion a jazz directamente.
5. Forma de pago que pase al jazz.
6. Dejar guardado el nro de comprobante en cuaderno, porque el nro interno no sirve.
7. Proceso para unificar codigos de productos. Definiendo el codigo que queda y el otro que es absorbido por el otro.


reconfiguracion de envio de medios de pago, la primer version de la implementacion se enviaba el recargo del producto y se generaba un unico producto ajuste.
Ahora se controla cada uno de manera independiente:

4hr: Configuracion del frontend para enviar los “recargos + no_recargos” → antes enviaba un objeto con {label, monto, recargo}

6hr: Se genera un producto de tipo ajuste por medio de pago que tenga recargo

2hr: seteo de descripcion producto del pedido: “label recargo - monto + recargo =  total a cobrar por medio de pago

Control de medios sin recargo:
4hr: controlar los medios sin recargo para no generar producto ajuste, y sin alterar el precio original (total)
2hr: concatena en la observacion del pedido la observacion original + detalle de los sin recargo (label y total) (utilizando @ como delimitador)
4hr: reconfiguracion de la api de generacion de pedido (principal) para enviar como observacion “lo que va despues del @”

Total: 22hr


