<?php

namespace Jivoo;

class UtilitiesTest extends \Jivoo\TestCase
{

    public function testConversions()
    {
      // Windows to unix style paths:
        $this->assertEquals('some/directory/file', Utilities::convertPath('some\directory\file'));
        $this->assertEquals('///', Utilities::convertPath('\\\\\\'));
    
      // CamelCase to lisp-case
        $this->assertEquals('do-stuff', Utilities::camelCaseToDashes('DoStuff'));
        $this->assertEquals('do-more-stuff', Utilities::camelCaseToDashes('doMoreStuff'));
        $this->assertEquals('h-t-m-l', Utilities::camelCaseToDashes('HTML'));
    
      // CamelCase to snake_case
        $this->assertEquals('do_stuff', Utilities::camelCaseToUnderscores('DoStuff'));
        $this->assertEquals('do_more_stuff', Utilities::camelCaseToUnderscores('doMoreStuff'));
        $this->assertEquals('h_t_m_l', Utilities::camelCaseToUnderscores('HTML'));
    
      // lisp-case to CamelCase
        $this->assertEquals('DoStuff', Utilities::dashesToCamelCase('do-stuff'));
        $this->assertEquals('DoMoreStuff', Utilities::dashesToCamelCase('do-more-stuff'));
        $this->assertEquals('HTML', Utilities::dashesToCamelCase('h-t-m-l'));
        $this->assertEquals('TEst', Utilities::dashesToCamelCase('t----est'));
    
      // snake_case to CamelCase
        $this->assertEquals('DoStuff', Utilities::underscoresToCamelCase('do_stuff'));
        $this->assertEquals('DoMoreStuff', Utilities::underscoresToCamelCase('do_more_stuff'));
        $this->assertEquals('HTML', Utilities::underscoresToCamelCase('h_t_m_l'));
        $this->assertEquals('TEst', Utilities::underscoresToCamelCase('t____est'));
    
      // Slugs
        $this->assertEquals('my-post-title', Utilities::stringToDashes('My Post-Title1234'));
    }
  
    public function testPrioritySorter()
    {
        $a = array('id' => 'a', 'priority' => 1);
        $b = array('id' => 'b', 'priority' => 5);
        $c = array('id' => 'c', 'priority' => 7);
        $array = array($a, $b, $c);
        usort($array, array('Jivoo\Utilities', 'prioritySorter'));
        $this->assertEquals(array($c, $b, $a), $array);
    }
}
