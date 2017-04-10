<?php

namespace App\Services;
/**
 * Class Trie
 */
class Trie
{
    private $trie = array();
    private $value = null;

    /**
     * Trie constructor.
     * @param mixed $value value to store
     */
    public function __construct($value = true)
    {
        $this->value = $value;
    }

    /**
    * Add value to the trie
    *
    * @param string $string
     * @param mixed $value
    */
    public function add($string, $value = true)
    {

        if ($string === "") {
            $this->value = $value;
            return;
        }
        foreach ($this->trie as $prefix => $trie) {
            $prefix = (string)$prefix;
            $prefixLength = strlen($prefix);
            $head = substr($string,0,$prefixLength);
            $headLength = strlen($head);
            $equals = true;
            $equalPrefix = "";
            for ($i = 0; $i<$prefixLength; $i++) {
                //Split
                if ($i >= $headLength) {
                $equalTrie = new Trie($value);
                $this->trie[$equalPrefix] = $equalTrie;
                $equalTrie->trie[substr($prefix,$i)] = $trie;
                unset($this->trie[$prefix]);
                return;
            } elseif ($prefix[$i] != $head[$i]) {
                if ($i > 0) {
                    $equalTrie = new Trie(null);
                    $this->trie[$equalPrefix] = $equalTrie;
                    $equalTrie->trie[substr($prefix,$i)] = $trie;
                    $equalTrie->trie[substr($string,$i)] = new Trie($value);
                    unset($this->trie[$prefix]);
                    return;
            }
            $equals = false;
            break;
            }
            $equalPrefix .= $head[$i];
            }
            if ($equals) {
            $trie->add(substr($string,$prefixLength), $value);
            return;
            }
        }
        $this->trie[$string] = new Trie($value);
    }

    /**
    * Search the Trie with a string
    *
    * @param $string string The string search
    *
    * @return mixed The value
    */
    public function search($string)
    {
        if (empty($string)) {
            return $this->value;
        }
        foreach ($this->trie as $prefix => $trie) {
            $prefixLength = strlen($prefix);
            $head = substr($string,0,$prefixLength);
            if ($head === $prefix) {
                return $trie->search(substr($string,$prefixLength));
            }
        }
        return null;
    }

}