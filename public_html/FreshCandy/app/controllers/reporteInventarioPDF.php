<?php
// reporteInventarioPDF.php
// Genera un PDF del inventario de ingredientes según los filtros seleccionados

// Incluir FPDF y los modelos necesarios
require_once __DIR__ . '/../assets/plugins/fpdf/fpdf.php';
require_once __DIR__ . '/../models/MySQL.php';
require_once __DIR__ . '/../models/Reporte.php';

// Recibir filtros por GET
$categoriaId = isset($_GET['categoriaId']) ? intval($_GET['categoriaId']) : null;
$estadoStock = isset($_GET['estadoStock']) ? intval($_GET['estadoStock']) : null;

// Obtener los datos filtrados usando el modelo
$mysql = new MySQL();
$reporte = new Reporte($mysql);
$data = $reporte->getInventarioIngredientes($categoriaId, $estadoStock);

// Crear el PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'Reporte de Inventario de Ingredientes', 0, 1, 'C');
$pdf->Ln(5);

// Encabezados de la tabla
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(40, 8, 'Ingrediente', 1);
$pdf->Cell(35, 8, 'Categoría', 1);
$pdf->Cell(25, 8, 'Stock', 1);
$pdf->Cell(25, 8, 'Unidad', 1);
$pdf->Cell(30, 8, 'Estado', 1);
$pdf->Ln();

// Datos de la tabla
$pdf->SetFont('Arial', '', 10);
foreach ($data as $row) {
    $pdf->Cell(40, 8, $row['nombre_ing'], 1);
    $pdf->Cell(35, 8, $row['titulo_categoria'], 1);
    $pdf->Cell(25, 8, $row['stock_ing'], 1);
    $pdf->Cell(25, 8, $row['nombre_unidad'], 1);
    $pdf->Cell(30, 8, $row['titulo_estado'], 1);
    $pdf->Ln();
}

// Salida del PDF en el navegador
$pdf->Output('I', 'reporte_inventario.pdf');
exit; 