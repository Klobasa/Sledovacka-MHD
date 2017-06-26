<?php

namespace App\Components;

interface SearchListControlFactory {

    /**
     * @return \App\Components\UI\SearchListGrid
     */
    function create();
}
