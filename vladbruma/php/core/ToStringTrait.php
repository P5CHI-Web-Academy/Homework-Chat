<?php
namespace Framework\Core;

defined('BASEPATH') OR exit('No direct script access allowed');

trait ToStringTrait {

    public function __toString() {

        foreach (array('title', 'name') as $prop) {
            if (property_exists($this, $prop)) {
                return (string) $this->$prop;
            }
        }

        return sprintf('%s #%s', get_class($this), $this->id);
    }
}