<?php
if(isset($errors))
{
    foreach($errors as $error)
    {
        ?>
        <div class="message my-3 bg-red-200 px-2 rounded py-3"><?=$error ?></div>
        <?php
    }
}