<?php

    namespace Alambagaskara\LoginManagement\App;

    use PHPUnit\Framework\TestCase;

    class ViewTest extends TestCase {

        public function testRender() {
            View::render("Home/index", [
                "Web Login Management"
            ]);

            $this->expectOutputRegex('[Web Login Management]');
            $this->expectOutputRegex('[html]');
            $this->expectOutputRegex('[body]');
            $this->expectOutputRegex('[Login Management]');
            $this->expectOutputRegex('[Register]');
            $this->expectOutputRegex('[Login]');
        }
    }