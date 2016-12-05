
<h2>Payment Instructions</h2>

<p>
    Please transfer the registration fee of <b>CHF <?=P($data["price"]);?>.&mdash;</b> with bank transfer.
</p>
<p>
    To:<br>
    Rechnungswesen der Universitat Zurich<br>
    LISA Symposium<br>
    8057 Zurich <br>
    IBAN-Nr.: CH12 0900 0000 3109 1810 4<br>
    Swift/BIC: POFICHBEXXX
</p>
<p>
    From / Message: <?=sprintf('%03d', intval($data["id"]));?> <?=P($data["lastname"]);?>
</p>
