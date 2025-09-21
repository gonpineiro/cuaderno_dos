###  Problemas en la sincronizacion de pedido -> Factura


1) En nuestro clientes tenemos el dato reference_id (ID + idClienteJazz), 
El usuario de nuestra base ID11561, 

{
    "dni": "18811647",
    "name": "TORRICO DONATO",
    "reference_id": ID11561
}

no se encuentra con ese ID dentro de Jazz, dentro de clientes de Jazz es el idCliente = 30425 y con otro atributo Numero = 17271


2) Segunda parte donde se deben agregar los productos del pedida ya creado, logre respectar la estructura de datos pero la api me retorna lo siguente:

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
