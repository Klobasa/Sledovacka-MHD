<?php

namespace App\Components;

interface AdminUsersControlFactory {

    /**
     * @return \App\Components\UI\AdminUsersGrid
     */
    function create();
}
