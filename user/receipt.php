<?php
/**
    Template used to create the invoice pdf
 */

require_once 'lib/auth.php';
require_once "../lib/app.php";

require_once "../lib/tcpdf_min/tcpdf_import.php";

if ($P['hasPayed'] != 1) {
    print "Not authorised";
    exit();
}


// Logos and images
$UZH_LOGO      = K_PATH_IMAGES . 'uzh_logo_e_pos_klein.png';
$FOOTER_IMG    = K_PATH_IMAGES . 'footer.png';
$HEADER_IMG    = K_PATH_IMAGES . 'header.png';
$SIGNATURE_IMG = K_PATH_IMAGES . 'sig_PhJ_bw.png';

$idnamestr  = sprintf( "%03d_%s", $P['id'], $P['lastname']);
$FILENAME = 'receipt_'.$idnamestr.'.pdf';

$LOCDATE = "Zurich, ".date("Y-m-d");


$PHY_ADDRESS = <<<EOD
<b>Physik-Institut</b><br />
Winterthurerstrasse 190<br />
8057 Z&uuml;rich<br />
Switzerland<br />
Phone: +41 44 635 57 81<br />
http://www.physik.uzh.ch/events/lisa2016<br />
relativityUZH@gmail.com
<br><br><br><br>
<br><br><br><br>
{$LOCDATE}<br>
<small>{$FILENAME}</small>
EOD;


$REC_ADRESS = explode("\n",
                preg_replace("/(?<=[^\r]|^)\n/", "\n",
                    $P['address']));

$SPECIAL_NOTE = '';
if (isset($_POST['addline'])) {
    $SPECIAL_NOTE = $_POST['addline'];
}

$TITLE = "Receipt";

$upperlast = strtoupper($P['lastname']);

$HTML = <<<EOT
To whom it may concern,<br>
<br>
This letter confirms that we received the payment of
<b>CHF {$P['price']}.00</b> from <br>
<br>
<b>{$P['firstname']} {$upperlast}</b><br>
<i>{$P['affiliation']}</i><br>
<br>
for the registration fee to attend the LISA Symposium XI, which took place from Sept. 5th to 9th 2016 at Irchel Campus, University of Zurich, Switzerland.
EOT;


$SIGNATURE_IMGPERS = [
    "Philippe Jetzer",
    "on behalf of the organizers"
];





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
$pdf->SetTitle('LISA Symposium 2016 Attendance Letter');
$pdf->SetSubject('LISA Symposium 2016 Attendance Letter');
$pdf->SetKeywords('LISA,Symposium,2016,attendance');

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

// ---------------------------------------------------------

$pdf->SetY(90);
$pdf->SetFont('helvetica', '', 12);
$pdf->Image($HEADER_IMG, $pdf->GetX(), $pdf->GetY(), $W, '', 'png', '', 'T', true, 300, '', false, false, 0, true, false, false);

$pdf->SetY(120);
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0,0,$TITLE,0,1);


$pdf->SetY(140);
$pdf->SetFont('helvetica', '', 12);
$pdf->writeHTML($HTML, true, false, true, false, '');


$SIGPOS = 200;
$SIGOFF = 20;

$pdf->SetY($SIGPOS);
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(0,0,"Sincerely,",0,1);
$pdf->SetY($SIGPOS+30);
$pdf->Cell(0,0,$SIGNATURE_IMGPERS[0],0,1);
$pdf->SetFont('helvetica', '', 8);
$pdf->Cell(0,0,$SIGNATURE_IMGPERS[1],0,1);

$pdf->Image($SIGNATURE_IMG, $LM-5, $SIGPOS+30-$SIGOFF, "", $SIGOFF, 'png', '', 'T', true, 300, '', false, false, 0, true, false, false);

// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output($FILENAME, 'I');

//============================================================+
// END OF FILE
//============================================================+
?>
