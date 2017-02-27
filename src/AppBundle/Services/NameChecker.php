<?php

namespace AppBundle\Services;

use AppBundle\Model\Name;
use AppBundle\Model\NamePiece;

class NameChecker
{
    public function checkName($comparableName, $blacklist = array(), $noiselist = array())
    {
        $closest = null;
        $comparableArray = array();
        
        foreach (explode(" ", strtolower($comparableName)) as $comparablePiece) {
            if (!in_array($comparablePiece, $noiselist) && strlen($comparablePiece)) {
                $comparableArray[] = $comparablePiece;
            }
        }
        
        foreach ($blacklist as $blacklister) {
            $name = new Name($blacklister);
            
            foreach ($name->getNamePieces() as $namePiece) {
                $this->checkPiece($namePiece, $comparableArray, $noiselist);
            }
            
            $name->setResult($this->evaluate($name, $comparableArray));
            
            if (!$closest || $closest->getResult() > $name->getResult()) {
                $closest = $name;
            }
//             dump($name);
            if ($closest->getResult() == 0) break;
        }
//         die;
        return $closest;
    }
    
    private function checkPiece(NamePiece $namePiece, $comparableArray, $noiselist)
    {
        $piece = $namePiece->getNamePiece();
        
        foreach ($comparableArray as $comparablePiece) {
            if (!strlen($comparablePiece)) continue;
            
            if (strlen($comparablePiece) == 1) {
                if ($comparablePiece == substr($piece, 0, 1)) {
                    $namePiece->addAbbreviation($comparablePiece);
                }
            } else {
                $levenshtein = levenshtein($piece, $comparablePiece);
                
                if ($namePiece->getLevenshtein() === null || $levenshtein < $namePiece->getLevenshtein()) {
                    $namePiece->setLevenshtein($levenshtein);
                }
            }
        }
    }
    
    private function evaluate($name, $comparableArray)
    {
        $result = 0;
        
        foreach ($name->getNamePieces() as $piece) {
            if ($piece->getAbbreviation()) {
                $result += 1;
            } elseif ($piece->getLevenshtein() >= strlen($piece->getNamePiece())) {
                $difference = abs(count($comparableArray) - count($name->getNamePieces()));
                $result += intval($difference * 5 - count($comparableArray));
            } else {
                $result += $piece->getLevenshtein();
            }
        }
        
//         $difference = abs(count($comparableArray) - count($name->getNamePieces()));
//         $comparableLength = strlen(implode("", $comparableArray));
        
// //         $result += intval($difference * ($comparableLength / 4));
//         if ($difference > 0) {
//             $result += intval($difference * 4 - count($comparableArray));
//         }
        
        return $result;
    }
}