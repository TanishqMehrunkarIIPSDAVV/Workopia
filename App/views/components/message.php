<?php
use Framework\Session;
$msgSuccess = Session::getFlash("message_success");
$msgError = Session::getFlash("message_error");
if ($msgSuccess !== null)
{
    ?>
        <div class="message bg-green-300 p-3 my-3 font-medium">
            <?= $msgSuccess ?>
        </div>
    <?php
}
else if ($msgError !== null)
{
    ?>
        <div class="message bg-red-300 p-3 my-3 font-medium">
            <?= $msgError ?>
        </div>
    <?php
}
?>