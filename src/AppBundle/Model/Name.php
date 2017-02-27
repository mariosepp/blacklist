<?php

namespace AppBundle\Model;

class Name
{
    private $full_name;
    private $name_pieces = array();
    private $result;
    
    
    public function __construct($blacklister)
    {
        $this->full_name = $blacklister;
        
        foreach (explode(' ', $blacklister) as $piece) {
            $namePiece = new NamePiece($this, $piece);
            $this->addNamePiece($namePiece);
        }
    }
    
    public function setFullName($full_name)
    {
        $this->full_name = $full_name;
        
        return $this;
    }
    
    public function getFullName()
    {
        return $this->full_name;
    }
    
    public function addNamePiece(NamePiece $name_piece)
    {
        $this->name_pieces[] = $name_piece;
        
        return $this;
    }
    
    public function getNamePieces()
    {
        return $this->name_pieces;
    }
    
    public function setResult($result)
    {
        $this->result = $result;
        
        return $this;
    }
    
    public function getResult()
    {
        return $this->result;
    }
}