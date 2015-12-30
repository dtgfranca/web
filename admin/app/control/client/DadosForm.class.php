<?php
/**
 * System_groupList Listing
 * @author  <your name here>
 */
class DadosForm extends TPage
{
    private $form;
    
    /**
     * Class constructor
     * Creates the page
     */
    function __construct()
    {
        parent::__construct();
        
        // create the form using TQuickForm class
        $this->form = new TQuickForm;
        $this->form->class = 'tform';
        $this->form->setFormTitle('Informações Pessoais');
        
        // create the form fields
        $id          = new TEntry('id');
        $nome         = new TEntry('name');
        $email       = new TEntry('email');
        $telefone        = new TEntry('phone');
        $usuario     = new TEntry('login');
        $date       = new TDate('date');
        $cep       = new TEntry('cep');
        $senha     = new TPassword('senha');
        $cSenha   = new TPassword('senha1');
      
        

        $telefone->setMask('(99)99999-9999');
        $cep->setMask('99.999-999');
        //$id->setValue(TSession::getValue('id'));
        //$usuario->setValue (TSession::getValue('login'));
        $id->setEditable(FALSE);
       
        
        // add the fields inside the form
         $this->form->addQuickField('Código',    $id,80);
        $this->form->addQuickField('Nome',    $nome,700);
        $this->form->addQuickField('Usuario',    $usuario,350);
        $this->form->addQuickField('Nova senha ',$senha,200);
        $this->form->addQuickField('Confirma senha',$cSenha,200);
        $this->form->addQuickField('E-mail', $email, 280);
        $this->form->addQuickField('Telefone', $telefone, 180);
        $this->form->addQuickField('CEP', $cep, 180);
        $this->form->addQuickField('Data De nascimento', $date, 180);
       
        
    
        
        
        
        // define the form action 
        $this->form->addQuickAction('Salvar', new TAction(array($this, 'onSave')), 'ico_save.png');
        
        // wrap the page content using vertical box
        $vbox = new TVBox;
        $vbox->style= "width:100%";
        $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $vbox->add($this->form);
       

        parent::add($vbox);
    }
    
    /**
     * Simulates an save button
     * Show the form content
     */
    public function onSave($param)
    {
       
        
        // put the data back to the form      
        
        try{
                TTransaction::open('permission');
                 $data = $this->form->getData('Dados'); // optional parameter: active record class
                 $data->store();
                 $this->form->setData($data);
                 TTransaction::close();
                 new TMessage('info', "Dados Cadastrados com sucesso");
         
        }catch(Exception  $e){
             
         // show the message
         new TMessage('info', $e->getMessage());
        }
      
       
    }
    public function onLoad(){
        
        try{
            TTransaction::open('permission');
            $id = TSession::getValue('id');
            $object = new Dados($id);
            $this->form->setData($object);
            TTransaction::close();
        }catch(Exception  $e){         
         new TMessage('info', $e->getMessage());
        }
      
    
    }
}
?>