<?php
/**
    Template used to create the invoice pdf
 */

require_once 'lib/auth.php';
require_once "../lib/app.php";

require_once "../lib/tcpdf_min/tcpdf_import.php";


// Logos and images
$UZH_LOGO      = K_PATH_IMAGES . 'uzh_logo_e_pos_klein.png';
$FOOTER_IMG    = K_PATH_IMAGES . 'footer.png';
$HEADER_IMG    = K_PATH_IMAGES . 'header.png';
$SIGNATURE_IMG = K_PATH_IMAGES . 'sig_PhJ_bw.png';


$PHY_ADDRESS = <<<EOD
<b>Physik-Institut</b><br />
Winterthurerstrasse 190<br />
8057 Z&uuml;rich<br />
Switzerland<br />
Phone: +41 44 635 57 81<br />
http://www.physik.uzh.ch/events/lisa2016<br />
relativityUZH@gmail.com
EOD;

$PAYMENT_INSTRUCTION_TITLE = "Please pay by bank transfer to:";

$PAYMENT_INSTRUCTIONS = [
    "Rechnungswesen der Universitat Zurich",
    "LISA Symposium",
    "8057 Zurich",
    "",
    "IBAN-Nr. : CH12 0900 0000 3109 1810 4",
    "Swift/BIC: POFICHBEXXX",
    "Message  : 000 LASTNAME",
];


$REC_ADRESS = [
    "Inst Name",
    "Person Name",
    "Street1",
    "Street2",
    "PLZ Loc",
    "Country"
];

$SPECIAL_NOTE = '';

$INVOICE_ITMS = [
    ["Conference fee", "CHF", "350.00"],
    ["Dinner for 1 accompaning person(s)", "CHF", "100.00"],
    ["Special agreement", "CHF", "-100.00"],
];

$TOTAL_AMNT = ["CHF", "350.00"];

$LOCDATE = "Zurich, ".date("Y-m-d");

$SIGNATURE_IMGPERS = [
    "Philippe Jetzer",
    "on behalf of the organizers"
];

// --------------------------------------------------------------------------
// read custion information from SESSION


$REC_ADRESS = explode("\n",
                preg_replace("/(?<=[^\r]|^)\n/", "\n",
                    $P['address']));

if (isset($_POST['addline'])) {
    $SPECIAL_NOTE = $_POST['addline'];
}

$INVOICE_ITMS = [];
$TOT = 0;

$regdate = new DateTime($P['registrationDate']);

$fee = ($regdate < $reducedLimitDate ? $baseFeeReduced : $baseFeeRegular) + $dinnerFee;
$cfi = [
    "Conference fee",
    "CHF",
    strval($fee).".00"
];
array_push($INVOICE_ITMS, $cfi);
$TOT += $fee;


# add conference dinner for additional persons
$addPersons = $P['nPersons']-1;
if ($addPersons>0) {
    $fee = 100*$addPersons;
    $cfi = [
        "+ Dinner for {$addPersons} additional person(s)",
        "CHF",
        strval($fee) . ".00"
    ];
    array_push($INVOICE_ITMS, $cfi);
    $TOT += $fee;
}

$price = $P['price'];
if ($price!=$TOT) {
    $diff = $price-$TOT;
    $cfi = [
        "+ Special arrangement",
        "CHF",
        strval($diff) . ".00"
    ];
    array_push($INVOICE_ITMS, $cfi);
    $TOT += $diff;
}


#TODO add more lines to finish the incoice
$TOTAL_AMNT = ["CHF", strval($TOT).".00"];


$LOCDATE = "Zurich, " . $regdate->format("Y-m-d");

$PAYMENT_INSTRUCTIONS = [
    "Rechnungswesen der Universitat Zurich",
    "LISA Symposium",
    "8057 Zurich",
    "",
    "IBAN-Nr. : CH12 0900 0000 3109 1810 4",
    "Swift/BIC: POFICHBEXXX",
];
$idnamestr  = sprintf( "%03d %s", $P['id'], $P['lastname']);
$idnamestr2 = sprintf( "%03d_%s", $P['id'], $P['lastname']);
array_push($PAYMENT_INSTRUCTIONS, "Message  : " . $idnamestr);






class MYPDF extends TCPDF {

    //Page header
    public function Header() {

        global $PHY_ADDRESS, $UZH_LOGO;
        $LEFT = $this->getMargins()['left'];
        $TOP = $this->getMargins()['header'];

        $this->Image($UZH_LOGO, $LEFT, $TOP, 60, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        $this->SetFont('helvetica', '', 10);
        $this->writeHTMLCell(0, 30, 120, $TOP, $PHY_ADDRESS, 0, 1, false, 'L');
    }

    // Page footer
    public function Footer() {
        global $FOOTER_IMG;

        $LM = $this->getMargins()['left'];
        $RM = $this->getMargins()['right'];
        $HM = $this->getMargins()['header'];
        $FM = $this->getMargins()['footer'];
        $W = $this->getPageWidth() - $RM - $LM;
        $H = $this->getPageHeight() - $HM - $FM;
        $LEFT = $this->getMargins()['left'];

        $this->Image($FOOTER_IMG, $LEFT, $H, $W, '', 'png', '', 'T', true, 300, '', false, false, 0, true, false, false);
    }

}

// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Rafael Kueng');
$pdf->SetTitle('LISA Symposium 2016 Invoice');
$pdf->SetSubject('LISA Symposium 2016 Invoice');
$pdf->SetKeywords('LISA,Symposium,2016,Invoice');

// set header and footer fonts
$pdf->setHeaderFont(Array("helvetica", '', "12"));
$pdf->setFooterFont(Array("helvetica", '', "12"));

// set default monospaced font
$pdf->SetDefaultMonospacedFont("courier");

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// ---------------------------------------------------------

$LM = $pdf->getMargins()['left'];
$RM = $pdf->getMargins()['right'];
$W = $pdf->getPageWidth() - $RM - $LM;

// ---------------------------------------------------------

$pdf->SetFont('helvetica', '', 12);
$pdf->AddPage();

// Print address
$pdf->SetFont('helvetica', '', 10);
foreach ($REC_ADRESS as $line) {
    $pdf->Cell(0,0,$line,0,1);
}
$pdf->SetFont('helvetica', '', 8);
$pdf->ln();
$pdf->Cell(0,0,$SPECIAL_NOTE,0,1);
$pdf->ln();

$pdf->SetY(90);
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0,0,"Invoice",0,1);

// ---------------------------------------------------------

$pdf->SetY(100);
$pdf->SetFont('helvetica', '', 12);
$pdf->Image($HEADER_IMG, $pdf->GetX(), $pdf->GetY(), $W, '', 'png', '', 'T', true, 300, '', false, false, 0, true, false, false);

$w2 = 20;
$pdf->SetY(140);

foreach($INVOICE_ITMS as $i) {
    $item = $i[0];
    $curr = $i[1];
    $amnt = $i[2];

    $pdf->Cell($W-2*$w2,0,$item,"B",0);
    $pdf->Cell($w2,0,$curr,"B",0,"R");
    $pdf->Cell($w2,0,$amnt,"B",1,"R");
}

$pdf->SetFont('helvetica', '', 2);
$pdf->Cell($W,1,"","B",1);

$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell($W-2*$w2,10,"TOTAL","B",0);
$pdf->Cell($w2,10,$TOTAL_AMNT[0],"B",0,"R");
$pdf->Cell($w2,10,$TOTAL_AMNT[1],"B",0,"R");
$pdf->Ln();


$pdf->SetY(180);
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell($W,10,$PAYMENT_INSTRUCTION_TITLE,0,1);

$pdf->SetFont('courier', 'B', 10);
foreach($PAYMENT_INSTRUCTIONS as $line){
    $pdf->Cell(0,0,$line,0,1);
}

$os = 120;
$pdf->SetY(250);
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell($os,0,$LOCDATE,0,0);
$pdf->Cell(0,0,$SIGNATURE_IMGPERS[0],0,1);
$pdf->SetFont('helvetica', '', 8);
$pdf->Cell($os,0,"",0,0);
$pdf->Cell(0,0,$SIGNATURE_IMGPERS[1],0,1);

$sh = 20;
$pdf->Image($SIGNATURE_IMG, $os+$LM-5, 250-$sh, "", $sh, 'png', '', 'T', true, 300, '', false, false, 0, true, false, false);

// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('invoice_'.$idnamestr2.'.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
?>
