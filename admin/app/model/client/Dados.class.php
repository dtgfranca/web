<?php

class Dados extends TRecord
{
   //sempre que construir um mode inicar com essa constante
     const TABLENAME ='system_user';
     const PRIMARYKEY='id';
     const IDPOLICY ='serial';
     
     public function __construct($id=NULL){
        parent::__construct($id);
        
        //campos do banco de dados
        parent::addAttribute('name');
        parent::addAttribute('login');
        parent::addAttribute('password');
        parent::addAttribute('email');
        parent::addAttribute('phone');
        parent::addAttribute('date');
     
     }


}

?>