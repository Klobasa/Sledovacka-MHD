<?php

namespace App\Components;

interface RecordListControlFactory {

    /**
     * @return \App\Components\UI\RecordsListGrid
     */
    function create();
}
