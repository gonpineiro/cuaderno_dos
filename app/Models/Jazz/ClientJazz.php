<?php

namespace App\Models\Jazz;

use Illuminate\Database\Eloquent\Model;

class ClientJazz extends Model
{
    protected $connection = 'jazz';
    protected $table = 'clientes';

    protected $fillable = [
        'IdCliente',
        'Numero',
        'Tipo',
        'Empresa',
        'NroInterno',
        'Nombre',
        'Domicilio',
        'CP',
        'Localidad',
        'Mail',
        'Telefono',
        'TelParticular',
        'TelCelular',
        'FechaNacimiento',
        'Obs',
        'DescuentoHabitual',
        'Alta',
        'Baja',
        'CUIT',
        'IVA_Tipo',
        'IdTransportista',
        'IdVendedor',
        'CodTransportista',
        'Vendedor',
        'Activo',
        'LimiteCred',
        'Provincia',
        'NroDocumento',
        'ISIB',
        'FechaMOD',
        'UserMOD',
        'FechaALTA',
        'UserALTA',
        'FechaBAJA',
        'UserBAJA',
        'IdLista',
        'WEB',
        'Mail2',
        'Fax',
        'TelefonoDeposito',
        'DomicilioDeposito',
        'NroCliente',
        'HorarioDeposito',
        'FotoPath',
        'Contacto',
        'IdZona',
        'IdActividad',
        'IdAbono',
        'IdDiasCC',
        'Inicio_Facturacion',
        'LetraFacturaAbono',
        'CondVenta',
        'Sexo',
        'TipoDoc',
        'IdCategoria',
        'CuentaCompras',
        'CuentaVentas',
        'IdPais',
        'ExentoIIBB',
        'CarpetaRelacionada',
        'IdRetencionGanancia',
        'TiempoEntrega',
        'PermitirFacturarenctacte',
        'IdRetencionIIBB',
        'AfectaInfoProntoPago',
        'Reservado',
        'EsInscrGcias',
        'IdOrigen',
        'EXGanDesde',
        'EXGanHasta',
        'IdTecnico',
        'EXRG5329',
        'DashCostos',
        'IdClienteCRM',
    ];


    public static function getSearchColumns()
    {
        return [
            'Nombre',
            'Domicilio',
            'CP',
            'Localidad',
            'Mail',
            'CUIT',
            'Telefono',
            'TelParticular',
            'TelCelular',
            'FechaNacimiento',
        ];
    }
}
