<?php
require_once __DIR__ . "/../BD/connexion.php";
require_once __DIR__ . "/../BD/FactureModel.php";

// Configuration des logs d'erreurs
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');
error_reporting(E_ALL);

// Vérifier si la facture doit être générée automatiquement
if (isset($_GET['generate_invoice']) && $_GET['generate_invoice'] == 1 && isset($_GET['factureID'])) {
    $factureID = $_GET['factureID'];
    $model = new FactureModel();
    $facture = $model->getFactureDetails($factureID);

    var_dump($facture); // Pour debug

    if (!$facture) {
        header('Location: ../IHM/ListeFactures.php?error=Facture+introuvable');
        exit;
    }

    $consommation = $facture['Qté_consommé'];
    $prixUnitaire = ($consommation <= 100) ? 0.82 : (($consommation <= 150) ? 0.92 : 1.1);
    $prixHT = $consommation * $prixUnitaire;
    $montantTVA = $prixHT * 0.18;
    $prixTTC = $prixHT + $montantTVA;

    // Chemin vers TCPDF
    $tcpdfPath = __DIR__ . '/../libs/tcpdf/tcpdf.php';
    if (!file_exists($tcpdfPath)) die("Erreur : Impossible de trouver TCPDF");
    require_once($tcpdfPath);

    // Classe personnalisée pour TCPDF
    class MYPDF extends TCPDF {
        public function Header() {
            // En-tête personnalisé
            $this->SetFont('helvetica', 'B', 24);
            $this->SetFillColor(52, 152, 219);
            $this->Rect(0, 0, $this->getPageWidth(), 30, 'F');
            $this->SetY(12);
            $this->SetTextColor(255, 255, 255);
            $leftMargin = 15;
            $imagePath = __DIR__ . '/lightning_logo.png';
            if (file_exists($imagePath)) {
                $this->Image($imagePath, $leftMargin, 10, 15, 15, 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
            }
            $this->SetX($leftMargin + 20);
            $this->Cell(0, 0, 'PowerBill', 0, 1, 'L');
            $this->SetY(30);
            $this->SetDrawColor(243, 156, 18);
            $this->SetLineWidth(1.5);
            $this->Line(10, 30, $this->getPageWidth() - 10, 30);
        }

        public function Footer() {
            // Pied de page personnalisé
            $this->SetY(-20);
            $this->SetFont('helvetica', '', 8);
            $this->SetTextColor(150, 150, 150);
            $this->SetDrawColor(243, 156, 18);
            $this->SetLineWidth(0.5);
            $this->Line(10, $this->GetY() - 5, $this->getPageWidth() - 10, $this->GetY() - 5);
            $this->Cell(0, 5, 'PowerBill - Votre partenaire énergétique', 0, 1, 'C');
            $this->Cell(0, 5, 'Tél: +212 5 22 123 456 - Email: contact@powerbill.ma', 0, 1, 'C');
            $this->Cell(0, 5, 'Page ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, 0, 'C');
        }
    }

    // Création du PDF
    $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A4', true, 'UTF-8', false);
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('PowerBill');
    $pdf->SetTitle('Facture #' . $factureID);
    $pdf->SetSubject('Facture d\'électricité');
    $pdf->AddPage();

    // Styles CSS intégrés
    $style = <<<EOD
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
    EOD;

    // Contenu HTML de la facture
    $html = $style . '
    <div class="invoice-header">
        <div class="invoice-title">FACTURE</div>
        <div class="invoice-number">N° ' . htmlspecialchars($factureID) . '</div>
    </div>

    <div class="info-box company-box">
        <div class="info-title">Société</div>
        <div class="info-content">
            <strong>PowerBill</strong><br>
            123 Avenue de l\'Énergie, Casablanca<br>
            Tél: +212 5 22 123 456<br>
            Email: contact@powerbill.ma<br>
            Site: www.powerbill.ma
        </div>
    </div>

    <div class="info-box client-box">
        <div class="info-title">Client</div>
        <div class="info-content">
            <strong>' . htmlspecialchars($facture['Nom'] . ' ' . $facture['Prénom']) . '</strong><br>
            Compteur: ' . htmlspecialchars($facture['ID_Compteur']) . '<br>
            Période: ' . htmlspecialchars($facture['Mois']) . ' ' . htmlspecialchars($facture['Annee']) . '<br>
            Date d\'émission: ' . htmlspecialchars($facture['Date_émission']) . '
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
                    <td class="center-align bold">' . htmlspecialchars($consommation) . ' kWh</td>
                    <td class="right-align bold">' . number_format($prixUnitaire, 4, ',', ' ') . ' DH/kWh</td>
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
                    <td class="amount">' . number_format($prixHT, 2, ',', ' ') . '</td>
                </tr>
                <tr class="summary-row">
                    <td colspan="2">TVA (18%)</td>
                    <td class="amount">' . number_format($montantTVA, 2, ',', ' ') . '</td>
                </tr>
                <tr class="border-top">
                    <td colspan="2" class="bold">Total TTC</td>
                    <td class="amount bold">' . number_format($prixTTC, 2, ',', ' ') . '</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="total-container">
        <div class="total-amount">
            Total à payer: ' . number_format($prixTTC, 2, ',', ' ') . ' DH
        </div>
    </div>

    <div class="payment-info">
        <strong>Informations de paiement:</strong><br>
        Paiement attendu avant le <span class="highlight">' . date('d/m/Y', strtotime($facture['Date_émission'] . ' +15 days')) . '</span><br>
        Mode de paiement: Virement bancaire ou Paiement en ligne
    </div>

    <div class="thank-you">
        Nous vous remercions pour votre confiance.<br>
    </div>';

    // Génération du PDF
    $pdf->writeHTML($html, true, false, true, false, '');
    $pdf->Output('facture_' . $factureID . '.pdf', 'D');
    exit;
} else {
    // Afficher une erreur ou une page par défaut si les paramètres ne sont pas corrects
    echo "Erreur: Paramètres manquants ou incorrects.";
}
?>