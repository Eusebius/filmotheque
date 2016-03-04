<?php
class Test extends PHPUnit_Framework_TestCase
{
    public function testCanBeNegated()
    {
        // Arrange
        $a = 0;

        // Act
        $a++;

        // Assert
        $this->assertEquals(1, $a);
    }
}