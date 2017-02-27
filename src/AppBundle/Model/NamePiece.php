<?php

namespace AppBundle\Model;

class NamePiece
{
    private $name;
    private $name_piece;
    private $abbreviation = array();
    private $levenshtein;
    
    
    public function __construct($name, $name_piece)
    {
        $this->name = $name;
        $this->name_piece = str_replace(array(',', '.'), '', $name_piece);
    }
    
    public function setName(Name $name)
    {
        $this->name = $name;
        
        return $this;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function setNamePiece($name_piece)
    {
        $this->name_piece = $name_piece;
        
        return $this;
    }
    
    public function getNamePiece()
    {
        return $this->name_piece;
    }
    
    public function setLevenshtein($levenshtein)
    {
        $this->levenshtein = $levenshtein;
        
        return $this;
    }
    
    public function getLevenshtein()
    {
        return $this->levenshtein;
    }
    
    public function addAbbreviation($abbreviation)
    {
        $this->abbreviation[] = $abbreviation;
    
        return $this;
    }
    
    public function getAbbreviation()
    {
        return $this->abbreviation;
    }
}