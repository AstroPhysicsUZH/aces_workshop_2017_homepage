        </div>
        <footer>
            <div class="log">
                <dl>
                    <dt>infolog:</dt>
                    <dd>
                        <?= implode("<br />\n", array_values($USER->info_log)); ?>
                    </dd>
                    <dt>errorlog:</dt>
                    <dd>
                        <?= implode("<br />\n", array_values($USER->error_log)); ?>
                    </dd>
                </dl>
            </div>
            <p>
                SCPS-AI - SimpleConferencePlanningSoftware with Admin Interface<br />
                <small>2016 &copy; Rafael Kueng; MIT license</small>
            </p>
        </footer>
    </div>

</body>
</html>


<?php
// Close file db connection
// -------------------------------------------------------------------------
$db = null;
?>
