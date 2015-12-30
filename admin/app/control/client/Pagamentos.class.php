<?php
class Pagamentos extends TPage
{
    private $form;
    
    function __construct(){
        parent::__construct();
         // create the form using TQuickForm class
        $this->form = new TQuickForm;
        $this->form->class = 'tform';     
        $this->form->setFormTitle('Formas de Pagamentos');
        
        $combo = new TCombo('pagamento');
        
        $combo_items = array();
        $combo_items['a'] ='Cartão de Crédito Visa';
        $combo_items['b'] ='Cartão de Crédito Mastercard';
        $combo_items['c'] ='Ticket Vale Refeição';
        $combo_items['d'] ='PagSeguro';
        $combo_items['e'] ='PayPal';
        $combo_items['f'] ='DriverCoins';
        $combo->addItems($combo_items);
        
        
        $this->form->addQuickField('Forma de Pagamentos',$combo,500);
        $this->form->addQuickAction('Salvar', new TAction(array($this, 'onSave')), 'ico_save.png');
        
        // wrap the page content using vertical box
        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $vbox->add($this->form);

        parent::add($vbox);
    
    }
    public function onSave($param)
    {
        $data = $this->form->getData(); // optional parameter: active record class
        
        // put the data back to the form
        $this->form->setData($data);
        
        // creates a string with the form element's values
        $message = 'Id: '           . $data->id . '<br>';
        $message.= 'Description : ' . $data->description . '<br>';
        $message.= 'Date1: '        . $data->date1 . '<br>';
        $message.= 'Date2: '        . $data->date2 . '<br>';
        $message.= 'Color : '       . $data->color . '<br>';
        $message.= 'List : '        . $data->list . '<br>';
        $message.= 'Text : '        . $data->text . '<br>';
        
        // show the message
        new TMessage('info', $message);
    }


}

?>