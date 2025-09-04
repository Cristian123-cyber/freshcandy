<?php
// reportePedidosProductoPDF.php
// Genera un PDF del reporte de pedidos por producto según los filtros seleccionados

require_once __DIR__ . '/../assets/plugins/fpdf/fpdf.php';
require_once __DIR__ . '/../models/MySQL.php';
require_once __DIR__ . '/../models/Reporte.php';

// Recibir filtros por GET
$fechaInicio = isset($_GET['fechaInicio']) ? $_GET['fechaInicio'] : null;
$fechaFin = isset($_GET['fechaFin']) ? $_GET['fechaFin'] : null;
$categoriaId = isset($_GET['categoriaId']) ? intval($_GET['categoriaId']) : null;

// Obtener los datos filtrados usando el modelo
$mysql = new MySQL();
$reporte = new Reporte($mysql);
$data = $reporte->getPedidosPorProducto($fechaInicio, $fechaFin, $categoriaId);

// Calcular el total general para el porcentaje
$totalGeneral = 0;
foreach ($data as $row) {
    $totalGeneral += $row['total_ventas'];
}

// Crear el PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'Reporte de Pedidos por Producto', 0, 1, 'C');
$pdf->Ln(5);

// Encabezados de la tabla
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(45, 8, 'Producto', 1);
$pdf->Cell(35, 8, 'Categoría', 1);
$pdf->Cell(30, 8, 'Unidades', 1);
$pdf->Cell(35, 8, 'Total Ventas', 1);
$pdf->Cell(25, 8, '% del total', 1);
$pdf->Ln();

// Datos de la tabla
$pdf->SetFont('Arial', '', 10);
foreach ($data as $row) {
    $pdf->Cell(45, 8, $row['nombre_producto'], 1);
    $pdf->Cell(35, 8, $row['nombre_categoria'] ?? $row['titulo_etiqueta'], 1);
    $pdf->Cell(30, 8, $row['unidades_vendidas'], 1);
    $pdf->Cell(35, 8, '$' . number_format($row['total_ventas'], 2), 1);
    $porcentaje = $totalGeneral > 0 ? round(($row['total_ventas'] / $totalGeneral) * 100) : 0;
    $pdf->Cell(25, 8, $porcentaje . '%', 1);
    $pdf->Ln();
}

$pdf->Output('I', 'reporte_pedidos_producto.pdf');
exit; 