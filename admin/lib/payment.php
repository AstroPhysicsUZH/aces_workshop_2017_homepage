<?php

// $db is already open!

if (!empty($_POST)) {

    #print_r($_POST);
    #print_r(getcwd());

    $lut = [
        'tp' => ["price"],
        'hp' => ["hasPayed"],
        'ap' => ["amountPayed"],
        'pn' => ["paymentNotes"]
    ];

    print "<h1>Saving changes</h1>";

    foreach ($_POST as $id => $vals) {

        $values = [];

        $stmtstr = "UPDATE {$tableName} SET ";

        $lbls = [];
        foreach ($vals as $sname => $val) {
            // $sname is the shortname used to POST data
            $pname = $lut[$sname][0]; // proper name in db
            $lbl = ":$sname";         // the label used in the statement string
            $lbls[] = "$pname = $lbl";
            $values[$lbl] = $val;
        }
        $stmtstr .= implode(", ", $lbls);

        # log entry
        $dtstr = $now->format($datetime_db_fstr);
        $str = "$dtstr\t{$_SESSION["username"]}\tupdate payment";
        $stmtstr .= ", notes = ('$str' || CHAR(13) || notes ) ";

        $stmtstr .= " WHERE id = :id;";

        #print_r($db_address);
        #print "\n<br />";
        #print_r($values);
        #print "\n<br />";
        #print_r($stmtstr);
        #print "\n<br />";

        $stmt = $db->prepare($stmtstr);
        $stmt->bindParam(':id', $id , PDO::PARAM_INT);
        foreach ($values as $lbl => $val) {
            $stmt->bindValue($lbl, $val);
        }

        $res = $stmt->execute();
        #print_r($res);
        print "updated ID $id: ";
        print_r($values);
        print "<br />";

        $res = null;
        $stmt = null;


        # send email to the user:
        $stmt = $db->prepare("SELECT ID, title, firstname, lastname, email, accessKey FROM {$tableName} WHERE id = :id" );
        $stmt->bindParam(":id", $id);
        $res = $stmt->execute();

        if (! $res) {
            echo "could not find email address. serious bug!!";
            die(1);
        }
        $p = $stmt->fetch(PDO::FETCH_OBJ);

        if (empty($p->isPassive)) { # empty() is true for NULL, "", [], 0, FALSE
            print "... sending email<br>";

            $from    = '"LISA Symposium Website" <relativityUZH@gmail.com>';
            $replyto = $from;

            $headers  = "";
            $headers .= 'From: ' . $from . "\r\n";
            $headers .= 'Reply-To:' . $replyto . "\r\n";
            $headers .= 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/plain; charset=UTF-8' . "\r\n";
            $headers .= 'X-Mailer: PHP/' . phpversion() . "\r\n";
            $headers .= 'Delivery-Date: ' . date("r") . "\r\n";

            $subject = "11th LISA Symposium Payment Update [{$p->id}]";

            $message = preg_replace('~\R~u', "\r\n",  # make sure we have RFC 5322 linebreaks
"Dear Mrs/Mr {$p->title} {$p->lastname}

Details about your payment for the 11th LISA Symposium were changed.
You can check the details here:\n"
. $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST']
. dirname(dirname($_SERVER['SCRIPT_NAME'])) # get one level up
. "/user/login.php?op=login&email=".urlencode($p->email)."&akey={$p->accessKey}&rdir=index.php

Kind regrads,
The local OK
");

            mail($p->email, $subject, $message, $headers);
        }

    }

    $db = null;
    print "<h2>done!</h2>";
    return;
}

// ----------------------------------------------------------------------------

$qry = $db->query(
    "SELECT ID, title, firstname, lastname, affiliation, email, price, hasPayed, amountPayed, paymentNotes
    FROM {$tableName} " . $addQuery . ";" );
$sel_people = $qry->fetchAll(PDO::FETCH_ASSOC);
$qry = null;

/*
$all_people = $db->query(
    "SELECT ID, title, firstname, lastname, affiliation, email, price, hasPayed, amountPayed
    FROM {$tableName}")->fetchAll(PDO::FETCH_ASSOC);
*/


function print_table($people) {
    //print_r($people);
    print "
    <table style='width:100%;'>
        <thead class='line_bot'>
            <th>ID</th>
            <th>title</th>
            <th>name</th>
            <th>affil</th>
            <th>has to pay<br />CHF</th>
            <th>has<br />payed?</th>
            <th>received<br />CHF</th>
            <th>Notes (user sees it!)</th>
            <th>edit</th>
        </thead>
        <tbody>
";

    foreach($people as $p) {
        //print_r($p);
        print("    <tr id='tr_{$p['id']}' class='line_bot'>\n");
        print("        <td class='center'>{$p['id']}</td>\n");
        print("        <td class='center'>{$p['title']}</td>\n");
        print("        <td>{$p['lastname']}<br />{$p['firstname']}</td>\n");
        print("        <td>{$p['affiliation']}</td>\n");
        print("
            <td class='center'>
                <input id='tp_{$p['id']}' name='tp_{$p['id']}'
                    class='' type='number' style='width:6em;height:2em;text-align:center;'
                    value='{$p['price']}' min='0' />
            </td>\n");
        print("
            <td class='center'>
                <input id='hp_{$p['id']}]' name='hp_{$p['id']}' class='left' type='checkbox' value='X'"
                . (( $p['hasPayed'] == 1 ) ? " checked" : "" )
                . " />
            </td>\n");
        print("
            <td class='center'>
                <input id='ap_{$p['id']}' name='ap_{$p['id']}'
                    class='' type='number' style='width:6em;height:2em;text-align:center;'
                    value='{$p['amountPayed']}' min='0' />
            </td>\n");
        print("
            <td class='center'>
                <input id='pn_{$p['id']}' name='pn_{$p['id']}'
                    class='' type='text' style='width:12em;height:2em;text-align:left;'
                    value='{$p['paymentNotes']}' />
            </td>\n");
        print("        <td class='center'>\n");
        print("            <a href='mailto:{$p['email']}'>mail</a>\n");
        print("            <a href='edit.php?id={$p['id']}'>edit</a>\n");
        //print("            <a href=''>del</a>\n");
        print("        </td>");

        print("    </tr>\n");
    }

    print "
        </tbody>
    </table>
";
}


?>

<script>

$(function() {

    data=[];
<?php
print "/* stores [fullname, hasPayed, price, amountPayed, paymentNotes]  and some refs */\n";
foreach($sel_people as $p) {
    $hasPayed = $p['hasPayed']==1 ? 'true': 'false';
    $price = empty($p['price']) ? '0': $p['price'];
    $amountPayed = empty($p['amountPayed']) ? '0' : $p['amountPayed'];
    $fullname = "\"{$p['lastname']}; {$p['firstname']} ({$p['title']})\"";
    print "    data[{$p['id']}] = {'fn':{$fullname}, 'hp':{$hasPayed}, 'tp':{$price}, 'ap':{$amountPayed}, 'pn':'{$p['paymentNotes']}', 'dirty':false};\n";
}
?>

    $.each( data, function(i, v){
        if (v==null) {return;}
        var o = $('#tr_'+i+' input');
        v.lnk = o;
        v.tr = $('#tr_'+i);
    });

    var form_dirty = false;

    $('#pay_frm').change( function() {

        form_dirty = false;

        $.each( data, function(i, db_value){

            if (db_value==null) {return;}

            var isDirty = false;
            var dirty = [];

            db_value.lnk.each(function(ii){

                var $formObj = $(db_value.lnk[ii]);
                var n = $formObj.attr('name').substr(0,2);
                console.log("" + i + " : "+n);
                var v1,v2;

                if (typeof(db_value[n]) === "boolean") {
                    v1 = db_value[n] ? 1 : 0;
                    v2 = $formObj.is(':checked') ? 1 : 0;
                }
                else if (typeof(db_value[n]) === "number"){
                    v1 = parseInt(db_value[n]);
                    v2 = parseInt($formObj.val()) || 0;
                }
                else if (typeof(db_value[n]) === "string"){
                    v1 = db_value[n];
                    v2 = $formObj.val() || "";
                }

                if ( v1 != v2 ){
                    isDirty = true;
                    dirty.push([n, v2]);
                }
                // console.log("   " + v1 + " --form: " + db_value[n] + " -- of type: " +typeof(db_value[n]));
                // console.log("   " + v2 + " --form: " + $formObj);

            })
            if (isDirty) {
                // console.log('dirty');
                db_value.tr.css({'background-color':'#faa', 'transition': 'background 0.5s linear'});
                db_value.dirty = dirty;
                form_dirty = true;

            }
            else {
                db_value.tr.css({'background-color':''});
                db_value.dirty = false;
            }
        });
    });

    $(window).on('beforeunload', function() {
        if (form_dirty){
            return "really? stuff is not changed!";
        }
    });


    ddata = [];

    $('#submit_pay_frm').click( function() {
        console.log("submit clicked" + "<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>");

        var $tmpform = $('<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" />');

        $.each( data, function(i, db_value){
            if (db_value==null) {return;}

            if (db_value.dirty) {
                $.each( db_value.dirty, function(ii, itm){
                    var propname = itm[0];
                    var val = itm[1];
                    console.log(db_value.fn + " / " + i + "; " + ii + "; " + propname + "; " + val);
                    if (!(i in ddata)) {
                        ddata[i] = {}
                    }
                    ddata[i][propname] = val;
                    $tmpform.append($('<input type="hidden" name="'+i+'['+propname+']" value="' + val + '">'));
                });
            }
        });

        // prevent dirty message:
        form_dirty = false;

        $tmpform.appendTo($(document.body)) //it has to be added somewhere into the <body>
                .submit();

        return false; // prevent sending of orginal form
    });

});


</script>

<h1>
    Manage Payments
</h1>

<ul class="pagemenu">
    <li><a href="payment_np.php">list only those that NOT payed already</a></li>
    <li><a href="payment_all.php">show all registered users</a></li>
</ul>

<p>
    Note that each user gets an email if his payment status changes!
</p>

<form id="pay_frm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
<input id="submit_pay_frm" type="submit" value="SAVE ALL CHANGES" class="save" style="width:100%; padding: 1em; background-color:#f66; border: 1px none #f00; border-radius: 5px;">

<h2 id="notpayed"><?=$h2tit?></h2>

<?php
print_table($sel_people);
?>

</form>
