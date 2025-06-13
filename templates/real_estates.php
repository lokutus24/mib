<div class="wrap"><h2>Ingatlanok</h2>

    <?php

        $myListTable = new Inc\Base\MibRealEstateListTable();
        $myListTable->prepare_items();
        $myListTable->display();
    ?>
</div>