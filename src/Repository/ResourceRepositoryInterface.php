<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Blast\Bundle\ResourceBundle\Repository;

/**
 *
 * @author glenn
 */
interface ResourceRepositoryInterface
{

    public function add($resource): void;
    public function remove($resource): void;
}
