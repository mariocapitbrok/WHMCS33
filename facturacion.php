<?php

use WHMCS\ClientArea;
use WHMCS\Module\Addon\Facturacom\Admin\CoreModule;

define('CLIENTAREA', true);

require __DIR__ . '/init.php';

$ca = new ClientArea();

$ca->setPageTitle('Factura tus servicios');
$ca->initPage();
$ca->requireLogin();

// Check login status
if ($ca->isLoggedIn()) {

    $CoreModule = new CoreModule;

    //variables de configuración
    $Settings = $CoreModule->getGonfiguration();
    $urlCallApi = $CoreModule->getURL($Settings);
    $systemURL = $CoreModule->getSystemURL();

    
    //traemos todas las facturas.
    $Invoices = $CoreModule->getInvoicesFacturacom($ca->getUserID(), null);
    
    //print_r($Invoices); die;
    $clientInvoices = [];

    //Todas las orders del usuario
    $OrdersWHMCS = $CoreModule->getWhmcsInvoices($ca->getUserID());

    //Todas las orders de wh
    $InvoicesWHMCS = $CoreModule->getWhmcsInvoicesAll($ca->getUserID());

    //object to array
    foreach ($Invoices['data'] as $key => $value) {
        if(array_key_exists($value['NumOrder'], $InvoicesWHMCS)){
            $clientInvoices[$key] = (array) $value;
        }
    }

    //Traemos los usos de CFDI
    $UsosCFDI = $CoreModule->getUsoCFDI();

    $ca->caching = false;
    $ca->assign('clientW', $ca->getUserID());
    $ca->assign('whmcsInvoices', $OrdersWHMCS);
    $ca->assign('clientInvoices', $clientInvoices);
    $ca->assign('systemURL', $systemURL);
    $ca->assign('apiUrl', $urlCallApi);
    $ca->assign('serieInvoices', $Settings['Serie']);
    $ca->assign('UsoCFDI', $Settings['UsoCFDI']);
    $ca->assign('Usos', $UsosCFDI);


} else {

    // User is not logged in
    echo "No tienes permisos para esta sección";

}

# Define the template filename to be used without the .tpl extension
$ca->setTemplate('customer_area/clientfacturacion');
$ca->output();
