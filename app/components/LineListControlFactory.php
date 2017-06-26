<?php

namespace App\Components;

interface LineListControlFactory {

    /**
     * @return \App\Components\UI\LinesListGrid
     */
    function create();
}
