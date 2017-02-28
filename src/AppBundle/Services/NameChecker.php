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
            
            $name->setResult($this->evaluateName($name, $comparableArray));
            
            if (!$closest || $closest->getResult() > $name->getResult()) {
                $closest = $name;
            }
            
            if ($closest->getResult() == 0) break;
        }
        
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
    
    private function evaluateName($name, $comparableArray)
    {
        $result = 0;
        $difference = count($comparableArray) - count($name->getNamePieces());
        
        foreach ($name->getNamePieces() as $piece) {
            if ($piece->getAbbreviation()) {
                $result += round(strlen($piece->getNamePiece()) * count($piece->getAbbreviation()) / 3);
            } elseif ($piece->getLevenshtein() >= strlen($piece->getNamePiece())) {
                $result += round(strlen($piece->getNamePiece()) / 2);
            } else {
                $result += $piece->getLevenshtein();
            }
        }
        
        if ($difference > 0) {
            $result += $difference * 3;
        }
        
        return intval($result);
    }
}