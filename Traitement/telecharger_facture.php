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
    header('Location: ../IHM/Client/view_bill.php');
    exit;
}

$factureID = intval($_GET['factureID']);
$facture = getDetailsFacture($pdo, $factureID);

if (!$facture) {
    header('Location: ../IHM/Client/view_bill.php');
    exit;
}



 // Calculate the invoice amounts
$consommation = $facture['Qté_consommé'];
$prixUnitaire = ($consommation <= 100) ? 0.82 : (($consommation <= 150) ? 0.92 : 1.1);
$prixHT = $consommation * $prixUnitaire;
$montantTVA = $prixHT * 0.18;
$prixTTC = $prixHT + $montantTVA;

class CustomTCPDF extends TCPDF {
    public function Header() {
        // Utilisation de la police helvetica standard
        $this->SetFont('helvetica', 'B', 24);
        
        // En-tête avec fond bleu
        $this->SetFillColor(52, 152, 219);
        $this->Rect(0, 0, $this->getPageWidth(), 30, 'F');
        $this->SetY(12);
        
        // Logo et titre alignés à gauche
        $this->SetTextColor(255, 255, 255);
        
        // Position de départ pour le contenu de gauche
        $leftMargin = 15; // Marge de 15mm depuis la gauche
        
        // Ajout de l'image à gauche
        $imagePath = __DIR__ . '/lightning_logo.png';
        if (file_exists($imagePath)) {
            $this->Image($imagePath, $leftMargin, 10, 15, 15, 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        }
        
        // Ajout du texte "PowerBill" juste après l'image
        $this->SetX($leftMargin + 20); // 20mm après la marge gauche (15 + 5mm d'espace)
        $this->Cell(0, 0, 'PowerBill', 0, 1, 'L'); // 'L' pour alignement à gauche
        
        // Ligne de séparation orange
        $this->SetY(30);
        $this->SetDrawColor(243, 156, 18);
        $this->SetLineWidth(1.5);
        $this->Line(10, 30, $this->getPageWidth()-10, 30);
    }

    public function Footer() {
        $this->SetY(-20);
        $this->SetFont('helvetica', '', 8);
        $this->SetTextColor(150, 150, 150);
        
        // Ligne de séparation
        $this->SetDrawColor(243, 156, 18);
        $this->SetLineWidth(0.5);
        $this->Line(10, $this->GetY()-5, $this->getPageWidth()-10, $this->GetY()-5);
        
        // Texte de pied de page
        $this->Cell(0, 5, 'PowerBill - Votre partenaire énergétique', 0, 1, 'C');
        $this->Cell(0, 5, 'Tél: +212 5 22 123 456 - Email: contact@powerbill.ma', 0, 1, 'C');
        $this->Cell(0, 5, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, 0, 'C');
    }
}

// Create a new PDF document using the custom TCPDF class
$pdf = new CustomTCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A4', true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('PowerBill');
$pdf->SetTitle('Facture #' . $factureID);
$pdf->SetSubject('Facture d\'électricité');
$pdf->AddPage();

// Define the HTML content for the PDF
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
    .info-box {
        margin-bottom: 20px;
        border-radius: 5px;
        padding: 15px;
        position: relative;
        overflow: hidden;
    }
    .info-box:before {
        content: "";
        position: absolute;
        left: 0;
        top: 0;
        width: 4px;
        height: 100%;
        background: #3498db;
    }
    .info-grid {
        display: grid;
        grid-template-columns: 120px 1fr;
        gap: 10px;
        align-items: center;
    }
    .info-label {
        color: #7f8c8d;
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
    .info-content {
        line-height: 1.6;
    }
    .table-container {
        margin: 20px 0;
    }
    .invoice-table {
        width: 100%;
        border-collapse: collapse;
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
    .invoice-table .amount {
        text-align: right;
        font-family: courier;
    }
    .invoice-table .right-align {
        text-align: right;
        padding-right: 20px;
    }
    .invoice-table .center-align {
        text-align: center;
    }
    .invoice-table tr:last-child td {
        border-bottom: none;
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
    .border-top {
        border-top: 1px solid #3498db;
    }
    .bold {
        font-weight: bold;
    }
    .summary-row {
        background-color: #f8f9fa;
    }
</style>

<div class="invoice-header">
    <div class="invoice-title">FACTURE</div>
   
</div>

<div class="info-box company-box">
    <div class="info-title">Société</div>
    <div class="info-content">
        <strong>PowerBill</strong><br>
        123 Avenue de l'Énergie, Casablanca<br>
        Tél: +212 5 22 123 456<br>
        Email: contact@powerbill.ma<br>
        Site: www.powerbill.ma
    </div>
</div>

<div class="info-box client-box">
    <div class="info-title">Client</div>
    <div class="info-content">
        <strong>{clientName}</strong><br>
        Compteur: {compteurID}<br>
        Période: {mois} {annee}<br>
        Date d'émission: {dateEmission}
    </div>
</div>

<div class="table-container">
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
                <td class="center-align bold">{consommation} kWh</td>
                <td class="right-align bold">{prixUnitaireFormatted} DH/kWh</td>
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
                <td class="amount">{prixHTFormatted}</td>
            </tr>
            <tr class="summary-row">
                <td colspan="2">TVA (18%)</td>
                <td class="amount">{montantTVAFormatted}</td>
            </tr>
            <tr class="border-top">
                <td colspan="2" class="bold">Total TTC</td>
                <td class="amount bold">{prixTTCFormatted}</td>
            </tr>
        </tbody>
    </table>
</div>

<div class="total-container">
    <div class="total-amount">
        Total à payer: {prixTTCFormatted} DH
    </div>
</div>

<div class="payment-info">
    <strong>Informations de paiement:</strong><br>
    Paiement attendu avant le <span class="highlight">{paymentDeadline}</span><br>
    Mode de paiement: Virement bancaire ou Paiement en ligne
</div>

<div class="thank-you">
    Nous vous remercions pour votre confiance.<br>
</div>
EOD;

// Vérifiez si 'Nom' et 'Prénom' existent dans le tableau
$nom = isset($facture['Nom']) ? $facture['Nom'] : 'Nom inconnu';
$prenom = isset($facture['Prenom']) ? $facture['Prenom'] : 'Prénom inconnu';

// Remplacer les placeholders dans le HTML
$html = str_replace('{factureID}', htmlspecialchars($factureID), $html);
$html = str_replace('{clientName}', htmlspecialchars($nom . ' ' . $prenom), $html);
$html = str_replace('{compteurID}', htmlspecialchars($facture['ID_Compteur']), $html);
$html = str_replace('{mois}', htmlspecialchars($facture['Mois']), $html);
$html = str_replace('{annee}', htmlspecialchars($facture['Annee']), $html);
$html = str_replace('{dateEmission}', htmlspecialchars($facture['Date_émission']), $html);
$html = str_replace('{consommation}', htmlspecialchars($consommation), $html);
$html = str_replace('{prixUnitaireFormatted}', number_format($prixUnitaire, 4, ',', ' '), $html);
$html = str_replace('{prixHTFormatted}', number_format($prixHT, 2, ',', ' '), $html);
$html = str_replace('{montantTVAFormatted}', number_format($montantTVA, 2, ',', ' '), $html);
$html = str_replace('{prixTTCFormatted}', number_format($prixTTC, 2, ',', ' '), $html);
$html = str_replace('{paymentDeadline}', date('d/m/Y', strtotime($facture['Date_émission'] . ' +15 days')), $html);


// Write the HTML content to the PDF
$pdf->writeHTML($html, true, false, true, false, '');

// Output the PDF
$pdf->Output('facture_' . $factureID . '.pdf', 'D');
exit;