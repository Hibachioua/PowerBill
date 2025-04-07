<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/../BD/connexion.php';
require_once __DIR__ . '/../BD/Factures.php';
require_once __DIR__ . '/../libs/tcpdf/tcpdf.php';

$pdo = connectDB();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (!isset($_GET['factureID']) || !is_numeric($_GET['factureID'])) {
    header('Location: ../IHM/ListeFactures.php');
    exit;
}

$factureID = intval($_GET['factureID']);
$facture = getDetailsFacture($pdo, $factureID);

if (!$facture) {
    header('Location: ../IHM/ListeFactures.php');
    exit;
}

// Calculate amounts with formatted numbers
$consommation = $facture['Qté_consommé'];
$prixUnitaire = ($consommation <= 100) ? 0.82 : (($consommation <= 150) ? 0.92 : 1.1);
$prixHT = $consommation * $prixUnitaire;
$montantTVA = $prixHT * 0.18;
$prixTTC = $prixHT + $montantTVA;

// Formatting helper function
function formatNumber($number, $decimals = 2) {
    return number_format($number, $decimals, ',', ' ');
}

class CustomTCPDF extends TCPDF {
    // Keep the same header/footer as in the second example
    public function Header() {
        $this->SetFont('helvetica', 'B', 24);
        $this->SetFillColor(52, 152, 219);
        $this->Rect(0, 0, $this->getPageWidth(), 30, 'F');
        $this->SetY(12);
        $this->SetTextColor(255, 255, 255);
        
        $imagePath = __DIR__ . '/lightning_logo.png';
        if (file_exists($imagePath)) {
            $this->Image($imagePath, 15, 10, 15, 15);
        }
        
        $this->SetX(35);
        $this->Cell(0, 0, 'PowerBill', 0, 1, 'L');
        $this->SetY(30);
        $this->SetDrawColor(243, 156, 18);
        $this->SetLineWidth(1.5);
        $this->Line(10, 30, $this->getPageWidth()-10, 30);
    }

    public function Footer() {
        $this->SetY(-20);
        $this->SetFont('helvetica', '', 8);
        $this->SetTextColor(150, 150, 150);
        $this->SetDrawColor(243, 156, 18);
        $this->Line(10, $this->GetY()-5, $this->getPageWidth()-10, $this->GetY()-5);
        $this->Cell(0, 5, 'PowerBill - Votre partenaire énergétique', 0, 1, 'C');
        $this->Cell(0, 5, 'Tél: +212 5 22 123 456 - Email: contact@powerbill.ma', 0, 1, 'C');
        $this->Cell(0, 5, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, 0, 'C');
    }
}

$pdf = new CustomTCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A4', true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetTitle('Facture #' . $factureID);
$pdf->AddPage();

// Payment deadline calculation
$paymentDeadline = date('d/m/Y', strtotime($facture['Date_émission'] . ' +15 days'));

$html = <<<EOD
<style>
    /* Keep all CSS styles from the second example */
    .invoice-header { background: linear-gradient(90deg, #3498db, #2980b9); color: white; padding: 15px; text-align: right; }
    .invoice-title { font-size: 28px; font-weight: bold; }
    .info-box { margin-bottom: 20px; padding: 15px; border-left: 4px solid; }
    .company-box { background-color: #f8f9fa; border-color: #3498db; }
    .client-box { background-color: #e8f4fc; border-color: #f39c12; }
    .invoice-table { width: 100%; border-collapse: collapse; }
    .invoice-table th { background-color: #3498db; color: white; padding: 12px; }
    .invoice-table td { padding: 12px; border-bottom: 1px solid #e0e0e0; }
    .amount { text-align: right; font-family: courier; }
    .total-amount { background-color: #f39c12; color: white; padding: 15px 25px; border-radius: 5px; }
    .highlight { color: #e74c3c; font-weight: bold; }
</style>

<div class="invoice-header">
    <div class="invoice-title">FACTURE</div>
    <div class="invoice-number">N° $factureID</div>
</div>

<div class="info-box company-box">
    <div class="info-title">Société</div>
    <div class="info-content">
        <strong>PowerBill</strong><br>
        123 Avenue de l'Énergie, Casablanca<br>
        Tél: +212 5 22 123 456<br>
        Email: contact@powerbill.ma
    </div>
</div>

<div class="info-box client-box">
    <div class="info-title">Client</div>
    <div class="info-content">
        <strong>{$facture['Nom']} {$facture['Prenom']}</strong><br>
        Compteur: {$facture['ID_Compteur']}<br>
        Date d'émission: {$facture['Date_émission']}
    </div>
</div>

<table class="invoice-table">
    <thead>
        <tr>
            <th width="45%">Détails de consommation</th>
            <th width="35%">Quantité</th>
            <th width="20%">Prix unitaire</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Consommation électrique</td>
            <td class="center-align bold">{$consommation} kWh</td>
            <td class="right-align bold">{$prixUnitaire} DH/kWh</td>
        </tr>
    </tbody>
</table>

<table class="invoice-table" style="margin-top: 30px;">
    <thead>
        <tr>
            <th colspan="2" width="80%">Récapitulatif</th>
            <th width="20%">Montant (DH)</th>
        </tr>
    </thead>
    <tbody>
        <tr class="summary-row">
            <td colspan="2">Total HT</td>
            <td class="amount">{$prixHT}</td>
        </tr>
        <tr class="summary-row">
            <td colspan="2">TVA (18%)</td>
            <td class="amount">{$montantTVA}</td>
        </tr>
        <tr class="border-top">
            <td colspan="2" class="bold">Total TTC</td>
            <td class="amount bold">{$prixTTC}</td>
        </tr>
    </tbody>
</table>

<div class="total-container">
    <div class="total-amount">
        Total à payer: {$prixTTC} DH
    </div>
</div>

<div class="payment-info">
    <strong>Informations de paiement:</strong><br>
    Paiement attendu avant le <span class="highlight">{$paymentDeadline}</span><br>
    Mode de paiement: Virement bancaire ou Paiement en ligne
</div>

<div class="thank-you">
    Nous vous remercions pour votre confiance.
</div>
EOD;

$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('facture_'.$factureID.'.pdf', 'I');