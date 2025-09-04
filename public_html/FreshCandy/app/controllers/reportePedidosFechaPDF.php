<?php
// reportePedidosFechaPDF.php
// Genera un PDF del reporte de pedidos por fecha según los filtros seleccionados

require_once __DIR__ . '/../assets/plugins/fpdf/fpdf.php';
require_once __DIR__ . '/../models/MySQL.php';
require_once __DIR__ . '/../models/Reporte.php';

// Recibir filtros por GET
$fechaInicio = isset($_GET['fechaInicio']) ? $_GET['fechaInicio'] : null;
$fechaFin = isset($_GET['fechaFin']) ? $_GET['fechaFin'] : null;
$estado = isset($_GET['estado']) ? intval($_GET['estado']) : null;

// Obtener los datos filtrados usando el modelo
$mysql = new MySQL();
$reporte = new Reporte($mysql);
$data = $reporte->getPedidosPorFecha($fechaInicio, $fechaFin, $estado);

// Crear el PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'Reporte de Pedidos por Fechas', 0, 1, 'C');
$pdf->Ln(5);

// Encabezados de la tabla
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(30, 8, 'ID Pedido', 1);
$pdf->Cell(35, 8, 'Fecha', 1);
$pdf->Cell(55, 8, 'Cliente', 1);
$pdf->Cell(35, 8, 'Total', 1);
$pdf->Cell(35, 8, 'Estado', 1);
$pdf->Ln();

// Datos de la tabla
$pdf->SetFont('Arial', '', 10);
foreach ($data as $row) {
    $pdf->Cell(30, 8, '#FC-' . $row['id_pedido'], 1);
    $pdf->Cell(35, 8, $row['fecha'], 1);
    $pdf->Cell(55, 8, $row['nombre_cliente'], 1);
    $pdf->Cell(35, 8, '$' . $row['monto_total'], 1);
    $pdf->Cell(35, 8, $row['titulo_estado'], 1);
    $pdf->Ln();
}

$pdf->Output('I', 'reporte_pedidos_fecha.pdf');
exit; 