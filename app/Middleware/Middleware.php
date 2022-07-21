<?php
    namespace Alambagaskara\LoginManagement\Middleware;

    interface Middleware {
        function before(): void;
    }