<?php

namespace App\Exceptions;

use Exception;

class WorkspaceNotOwnedException extends Exception {
    protected $message = "Vous n'avez pas accès à ce workspace ou celui-ci n'existe pas";
    protected $code = "402";
}
