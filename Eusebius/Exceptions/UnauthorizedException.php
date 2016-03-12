<?php

/**
 * Eusebius/Exceptions/UnauthorizedException.php
 * 
 * @author Eusebius <eusebius@eusebius.fr>
 * @since 0.3.2
 * 
 * Exception thrown when an action is attempted by a user without the permission to perform it.
 */
/*
  Filmothèque
  Copyright (C) 2012-2016 Eusebius (eusebius@eusebius.fr)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 2 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License along
  with this program; if not, write to the Free Software Foundation, Inc.,
  51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */

namespace Eusebius\Exceptions;

use Exception;

//TODO use this exception more extensively in the application

/**
 * Exception thrown when an action is attempted by a user without the permission to perform it.
 *
 * @author Eusebius <eusebius@eusebius.fr>
 * @since 0.3.2
 */
class UnauthorizedException extends Exception {

    public function __construct($message) {
        parent::__construct($message);
    }

}
