<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../BD/connexion.php';
require_once __DIR__ . '/../BD/Factures.php';
require_once  '/../libs/tcpdf/tcpdf.php';

if (!isset($_GET['factureCompID']) || !is_numeric($_GET['factureCompID'])) {
    header("HTTP/1.1 400 Bad Request");
    exit;
}

$factureID = (int)$_GET['factureCompID'];
$pdo = connectDB();

class ComplementaireTCPDF extends TCPDF {
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
        $this->Line(10, 30, $this->getPageWidth() - 10, 30);
    }

    public function Footer() {
        $this->SetY(-20);
        $this->SetFont('helvetica', '', 8);
        $this->SetTextColor(150, 150, 150);
        $this->SetDrawColor(243, 156, 18);
        $this->Line(10, $this->GetY() - 5, $this->getPageWidth() - 10, $this->GetY() - 5);
        $this->Cell(0, 5, 'PowerBill - Votre partenaire énergétique', 0, 1, 'C');
        $this->Cell(0, 5, 'Tél: +212 5 22 123 456 - Email: contact@powerbill.ma', 0, 1, 'C');
        $this->Cell(0, 5, 'Page ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, 0, 'C');
    }
}

try {
    $facture = getDetailsFactureComplementaire($pdo, $factureID);

    if (!$facture) {
        header("HTTP/1.1 404 Not Found");
        exit;
    }

    // Données client
    $nom         = $facture['Nom'] ?? 'Non renseigné';
    $prenom      = $facture['Prenom'] ?? '';
    $adresse     = $facture['Adresse'] ?? 'Adresse non disponible';
    $compteur    = $facture['ID_Compteur'] ?? 'N/A';
    $reference   = $facture['Reference'] ?? 'Aucune référence';
    $description = $facture['Description'] ?? 'Ajustement de consommation';

    // Calculs
    $consommation = $facture['Consommation'];
    $prixHT       = $facture['Prix_HT'];
    $montantTVA   = $prixHT * 0.18;
    $prixTTC      = $facture['Prix_TTC'];
    $tvaRate      = ($prixHT > 0) ? round(($montantTVA / $prixHT) * 100, 2) : 0;

    // Dates
    $dateEmission = $facture['Date_émission'] ?? date('Y-m-d');
    try {
        $paymentDeadline = date('d/m/Y', strtotime($dateEmission . ' +15 days'));
    } catch (Exception $e) {
        $paymentDeadline = 'Date invalide';
    }

    $pdf = new ComplementaireTCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A4', true, 'UTF-8', false);
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetTitle("Facture Complémentaire #$factureID");
    $pdf->AddPage();

    $html = <<<EOD
    <style>
        .invoice-header {
            background: linear-gradient(90deg, #3498db, #2980b9);
            color: white;
            padding: 20px;
            border-radius: 5px 5px 0 0;
            margin-bottom: 30px;
            text-align: right;
        }
        .invoice-title {
            font-size: 20px;
            font-weight: bold;
            margin: 5px 0;
            letter-spacing: 1px;
        }
        .info-box {
            margin-bottom: 30px;
            padding: 25px;
            border-left: 4px solid;
            border-radius: 5px;
        }
        .company-box {
            background-color: #f8f9fa;
            border-color: #3498db;
        }
        .client-box {
            background-color: #e8f4fc;
            border-color: #f39c12;
            margin-top: 20px;
        }
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin: 30px 0;
        }
        .invoice-table th {
            background-color: #3498db;
            color: white;
            padding: 15px;
            font-weight: 600;
        }
        .invoice-table td {
            padding: 15px;
            border-bottom: 2px solid #e0e0e0;
        }
        .amount {
            text-align: right;
            font-family: courier;
            font-weight: bold;
        }
        .total-container {
            margin: 40px 0;
            text-align: right;
        }
        .total-amount {
            background-color: #f39c12;
            color: white;
            padding: 20px 30px;
            border-radius: 5px;
            font-size: 22px;
            font-weight: bold;
            box-shadow: 0 3px 15px rgba(243, 156, 18, 0.3);
            display: inline-block;
        }
        .highlight {
            color: #e74c3c;
            font-weight: bold;
        }
        .payment-info {
            margin: 40px 0;
            padding: 25px;
            background-color: #f8f9fa;
            border-radius: 5px;
            border-left: 4px solid #2ecc71;
        }
        .thank-you {
            text-align: center;
            margin: 50px 0 30px;
            font-style: italic;
            color: #7f8c8d;
            font-size: 16px;
        }
        .spacer {
            height: 25px;
        }
        .bold {
            font-weight: bold;
        }
    </style>

    <div class="invoice-header">
        <div class="invoice-title">FACTURE COMPLÉMENTAIRE</div>
    </div>

    <div class="info-box company-box">
        <div class="info-title"><strong>SOCIÉTÉ</strong></div>
        <div class="spacer"></div>
        <div class="info-content">
            <strong>PowerBill</strong><br>
            123 Avenue de l'Énergie, Casablanca<br>
            Tél: +212 5 22 123 456<br>
            Email: contact@powerbill.ma
        </div>
    </div>

    <div class="spacer"></div>

    <div class="info-box client-box">
        <div class="info-title"><strong>CLIENT</strong></div>
        <div class="spacer"></div>
        <div class="info-content">
            Nom Complet : <strong>$nom $prenom</strong><br>
            Adresse : <strong>$adresse</strong><br>
            Compteur : <strong>$compteur</strong><br>
            Référence : <strong>$reference</strong><br>
            Date d'émission : <strong>$dateEmission</strong>
        </div>
    </div>

    <div class="spacer"></div>

    <table class="invoice-table">
        <thead>
            <tr>
                <th width="70%">Description</th>
                <th width="30%">Montant (DH)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>$description</td>
                <td class="amount bold">$consommation</td>
            </tr>
        </tbody>
    </table>

    <div class="spacer"></div>

    <table class="invoice-table">
        <thead>
            <tr>
                <th colspan="2" width="80%">Récapitulatif</th>
                <th width="20%">Montant (DH)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="2">Total HT</td>
                <td class="amount">%PRIX_HT%</td>
            </tr>
            <tr>
                <td colspan="2">TVA (%TAUX_TVA%%)</td>
                <td class="amount">%MONTANT_TVA%</td>
            </tr>
            <tr>
                <td colspan="2" class="bold">Total TTC</td>
                <td class="amount bold">%PRIX_TTC%</td>
            </tr>
        </tbody>
    </table>

    <div class="total-container">
        <div class="total-amount">
            Total à payer: %PRIX_TTC% DH
        </div>
    </div>

    <div class="payment-info">
        <strong>INFORMATIONS DE PAIEMENT :</strong><br><br>
        ➤ Paiement attendu avant le <span class="highlight">$paymentDeadline</span><br>
        ➤ Mode de paiement: Virement bancaire ou Paiement en ligne
    </div>

    <div class="thank-you">
        Nous vous remercions pour votre confiance et votre fidélité.
    </div>
    EOD;

    // Remplacement des variables
    $replacements = [
        '%PRIX_HT%'     => number_format($prixHT, 2, ',', ' '),
        '%MONTANT_TVA%' => number_format($montantTVA, 2, ',', ' '),
        '%PRIX_TTC%'    => number_format($prixTTC, 2, ',', ' '),
        '%TAUX_TVA%'    => $tvaRate
    ];

    $html = str_replace(array_keys($replacements), array_values($replacements), $html);

    $pdf->writeHTML($html, true, false, true, false, '');
    $pdf->Output("facture_complementaire_{$factureID}.pdf", 'D');

} catch (PDOException $e) {
    error_log("Erreur PDO : " . $e->getMessage());
    header("HTTP/1.1 500 Internal Server Error");
    exit;

} catch (Exception $e) {
    error_log("Erreur générique : " . $e->getMessage());
    header("HTTP/1.1 500 Internal Server Error");
    exit;
}