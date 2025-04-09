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
    header('Location: ../IHM/Client/ListeFactures.php');
    exit;
}

$factureID = intval($_GET['factureID']);
$facture = getDetailsFactureComplementaire($pdo, $factureID);

if (!$facture) {
    header('Location: ../IHM/Client/ListeFactures.php');
    exit;
}

// Calcul des montants
$consommation = $facture['Qté_consommé'] ?? 0;
$prixUnitaire = ($consommation <= 100) ? 0.82 : (($consommation <= 150) ? 0.92 : 1.1);
$prixHT = $consommation * $prixUnitaire;
$montantTVA = $prixHT * 0.18;
$prixTTC = $prixHT + $montantTVA;

// Formatage des nombres
function formatNumber($number, $decimals = 2) {
    return number_format($number, $decimals, ',', ' ');
}

// Date limite de paiement (+15 jours)
$paymentDeadline = date('d/m/Y', strtotime($facture['Date_emission'] . ' +15 days'));

class CustomTCPDF extends TCPDF {
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
        $this->SetLineWidth(0.5);
        $this->Line(10, $this->GetY()-5, $this->getPageWidth()-10, $this->GetY()-5);
        
        $this->Cell(0, 5, 'PowerBill - Votre partenaire énergétique', 0, 1, 'C');
        $this->Cell(0, 5, 'Tél: +212 5 22 123 456 - Email: contact@powerbill.ma', 0, 1, 'C');
        $this->Cell(0, 5, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, 0, 'C');
    }
}

$pdf = new CustomTCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A4', true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('PowerBill');
$pdf->SetTitle('Facture Complémentaire #' . $factureID);
$pdf->AddPage();

$html = <<<EOD
<style>
    .invoice-header {
        background: linear-gradient(90deg, #3498db, #2980b9);
        color: white;
        padding: 15px;
        border-radius: 5px 5px 0 0;
        margin-bottom: 20px;
        text-align: right;
    }
    .invoice-title {
        font-size: 28px;
        font-weight: bold;
        margin-bottom: 5px;
        letter-spacing: 1px;
    }
    .invoice-number {
        font-size: 16px;
        opacity: 0.9;
    }
    .complementary-badge {
        background: #e74c3c;
        color: white;
        padding: 5px 10px;
        border-radius: 3px;
        font-size: 14px;
        margin: 5px 0;
        display: inline-block;
    }
    .info-box {
        margin-bottom: 20px;
        border-radius: 5px;
        padding: 15px;
        position: relative;
        overflow: hidden;
    }
    .company-box {
        background-color: #f8f9fa;
        border-left: 4px solid #3498db;
    }
    .client-box {
        background-color: #e8f4fc;
        border-left: 4px solid #f39c12;
    }
    .info-title {
        font-weight: bold;
        color: #2c3e50;
        margin-bottom: 10px;
        font-size: 16px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .invoice-table {
        width: 100%;
        border-collapse: collapse;
        margin: 20px 0;
    }
    .invoice-table th {
        background-color: #3498db;
        color: white;
        padding: 12px 15px;
        font-weight: 600;
    }
    .invoice-table td {
        padding: 12px 15px;
        border-bottom: 1px solid #e0e0e0;
    }
    .right-align {
        text-align: right;
    }
    .center-align {
        text-align: center;
    }
    .bold {
        font-weight: bold;
    }
    .total-container {
        margin-top: 30px;
        text-align: right;
    }
    .total-amount {
        display: inline-block;
        background-color: #f39c12;
        color: white;
        padding: 15px 25px;
        border-radius: 5px;
        font-size: 20px;
        font-weight: bold;
        box-shadow: 0 3px 10px rgba(243, 156, 18, 0.2);
    }
    .payment-info {
        margin-top: 30px;
        padding: 15px;
        background-color: #f8f9fa;
        border-radius: 5px;
        border-left: 4px solid #2ecc71;
    }
    .thank-you {
        text-align: center;
        margin-top: 30px;
        font-style: italic;
        color: #7f8c8d;
        font-size: 14px;
    }
    .highlight {
        color: #e74c3c;
        font-weight: bold;
    }
</style>

<div class="invoice-header">
    <div class="invoice-title">FACTURE COMPLÉMENTAIRE</div>
    <div class="complementary-badge">Rectificative</div>
    <div class="invoice-number">N° $factureID</div>
    <div class="invoice-number">Date d'émission : {$facture['Date_emission']}</div>
</div>

<div class="info-box company-box">
    <div class="info-title">Société</div>
    <div class="info-content">
        <strong>PowerBill</strong><br>
        123 Avenue de l'Énergie, Casablanca<br>
        Tél: +212 5 22 123 456
    </div>
</div>

<div class="info-box client-box">
    <div class="info-title">Client</div>
    <div class="info-content">
        <strong>{$facture['Nom']} {$facture['Prenom']}</strong><br>
        CIN: {$facture['CIN']}<br>
        Adresse: {$facture['Adresse']}
    </div>
</div>

<table class="invoice-table">
    <thead>
        <tr>
            <th width="45%">Description</th>
            <th width="20%">Consommation</th>
            <th width="20%">Prix unitaire</th>
            <th width="15%">Total TTC</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Ajustement de consommation</td>
            <td class="center-align bold">{$consommation} kWh</td>
            <td class="right-align bold">{$prixUnitaire} DH</td>
            <td class="right-align bold">{$prixTTC} DH</td>
        </tr>
    </tbody>
</table>

<table class="invoice-table" style="margin-top: 30px;">
    <thead>
        <tr>
            <th colspan="3" width="80%">Récapitulatif</th>
            <th width="20%">Montant (DH)</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="3">Total HT</td>
            <td class="right-align">{$prixHT}</td>
        </tr>
        <tr>
            <td colspan="3">TVA (18%)</td>
            <td class="right-align">{$montantTVA}</td>
        </tr>
        <tr class="bold">
            <td colspan="3">Total à régulariser</td>
            <td class="right-align highlight">{$prixTTC} DH</td>
        </tr>
    </tbody>
</table>

<div class="payment-info">
    <strong>Informations de paiement :</strong><br>
    Paiement attendu avant le <span class="highlight">{$paymentDeadline}</span><br>
    Mode de paiement: Virement bancaire ou Paiement en ligne
</div>

<div class="thank-you">
    Nous vous remercions pour votre confiance.<br>
    Pour toute question, contactez-nous à <span class="highlight">contact@powerbill.ma</span>
</div>

EOD;

$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('facture_complementaire_'.$factureID.'.pdf', 'I');