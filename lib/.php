<?php
/**
 * Autoloader
 * Version 1.2
 * 217 bytes
 *
 * Copyright (c) 2020 Reinir Puradinata
 * All rights reserved
 */
spl_autoload_register(function($s){is_file($s=__DIR__.strtr("/$s.php",'\\','/'))&&require$s;});