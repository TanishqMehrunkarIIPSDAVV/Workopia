<?php
if (isset($_SESSION["message_success"]))
{
    ?>
        <div class="message bg-green-300 p-3 my-3 font-medium">
            <?= $_SESSION["message_success"] ?>
        </div>
    <?php
        unset($_SESSION["message_success"]);
}
else if (isset($_SESSION["message_error"]))
{
    ?>
        <div class="message bg-red-300 p-3 my-3 font-medium">
            <?= $_SESSION["message_error"] ?>
        </div>
    <?php
        unset($_SESSION["message_error"]);
}
?>