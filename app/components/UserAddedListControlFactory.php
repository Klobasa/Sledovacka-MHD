<?php

namespace App\Components;

interface UserAddedListControlFactory {

    /**
     * @return \App\Components\UI\UserAddedListGrid
     */
    function create();
}