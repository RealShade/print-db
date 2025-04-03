<?php

namespace App\Enums;

enum PrintTaskEventSource : int
{

    case MANUAL = 0; // Ручне
    case API = 1; // API

}
