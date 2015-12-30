<?php

class Enderecos extends TRecord
{
    const TABLENAME ='system_address';
    const PRIMARYKEY='id';
    const IDPOLICY='serial';
    
    public function __contruct($id= NULL){
        parent::__construct($id);
        parent::addAttribute('rua');
        parent::addAttribute('cep');
        parent::addAttribute('bairro');
        parent::addAttribute('complemento');
        parent::addAttribute('system_user_id');
        
    }

}

?>