<?php
class EnderecoForm extends TPage
{ private $form;      // registration form
    private $datagrid;  // listing
    private $loaded;
    
    /**
     * Class constructor
     * Creates the page, the form and the listing
     */
    public function __construct()
    {
        parent::__construct();
        
        // create the form
        $this->form = new TQuickForm('form_categories');
        $this->form->class = 'tform'; // CSS class
        $this->form->style = 'width: 100%';
        $this->form->setFormTitle('Lista de Endereços');
        
        // create the form fields
        $id     = new TEntry('id');
        $name   = new TEntry('name');
        $cep    = new TEntry('cep');
        $rua    = new TEntry('rua');
        $bairro = new TEntry('bairro');
        $numero = new TEntry('numero');
        $cUser  = new THidden('system_user_id');
        $complemento = new TText('complemento');
        
        
        
        #$name->addValidation('Name', new TRequiredValidator);
        $cep->setMask('99.999-999');
        $numero->setMask('999999999');
        $cUser->setValue(TSession::getValue('id'));
        // add the fields in the form
        $this->form->addQuickField('ID',    $id,    40);
        $this->form->addQuickField('cUser',  $cUser, 40);
        $this->form->addQuickField('Cep',  $cep, 200);
        $this->form->addQuickField('Rua',  $rua, 200);
        $this->form->addQuickField('Bairro',  $bairro, 200);
        $this->form->addQuickField('Numero',  $numero, 80);
        $this->form->addQuickField('Complemento',$complemento, 200);
        $complemento->setSize(400, 70);
        
        // create the form actions
        $this->form->addQuickAction('Salvar', new TAction(array($this, 'onSave')), 'ico_save.png');
        $this->form->addQuickAction('Novo',  new TAction(array($this, 'onEdit')), 'ico_new.png');
        
        // id not editable
        $id->setEditable(FALSE);
        
        // create the datagrid
        $this->datagrid = new TQuickGrid;
        $this->datagrid->setHeight(320);
        $this->datagrid->style= "width:100%";
        
        // add the datagrid columns
        $this->datagrid->addQuickColumn('Rua',   'rua',  'center', 50, new TAction(array($this, 'onReload')), array('order', 'id'));
        $this->datagrid->addQuickColumn('Bairro', 'bairro','left',  390, new TAction(array($this, 'onReload')), array('order', 'bairro'));
        $this->datagrid->addQuickColumn('Complemento', 'complemento','left',  390, new TAction(array($this, 'onReload')), array('order', 'complemento'));
        $this->datagrid->addQuickColumn('Numero', 'numero','left',  390, new TAction(array($this, 'onReload')), array('order', 'numero'));
        $this->datagrid->addQuickColumn('CEP', 'cep','left',  390, new TAction(array($this, 'onReload')), array('order', 'cep'));
        
        // add the datagrid actions
        $this->datagrid->addQuickAction('Editar',  new TDataGridAction(array($this, 'onEdit')),   'id', 'ico_edit.png');
        $this->datagrid->addQuickAction('Deletar', new TDataGridAction(array($this, 'onDelete')), 'id', 'ico_delete.png');
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        // wrap objects
        $table = new TTable;
        $table->style= "width:100%";
        $table->addRow()->addCell(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $table->addRow()->addCell($this->form);
        $table->addRow()->addCell($this->datagrid);
        
        // add the table in the page
        parent::add($table);
    }
    
    /**
     * method onReload()
     * Load the datagrid with the database objects
     */
    function onReload($param = NULL)
    {
        try
        {
            // open a transaction with database 'samples'
            TTransaction::open('permission');
            
            // creates a repository for Category
            $repository = new TRepository('Enderecos');
            
            // creates a criteria, ordered by id
            $criteria = new TCriteria;
            $order    = isset($param['order']) ? $param['order'] : 'id';
            $criteria->add(new TFilter('system_user_id', '=', TSession::getValue('id')));
            $criteria->setProperty('order', $order);
            
            // load the objects according to criteria
            $categories = $repository->load($criteria);
            $this->datagrid->clear();
            if ($categories)
            {
                // iterate the collection of active records
                foreach ($categories as $category)
                {
                    // add the object inside the datagrid
                    $this->datagrid->addItem($category);
                }
            }
            // close the transaction
            TTransaction::close();
            $this->loaded = true;
        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', '<b>Error</b> ' . $e->getMessage());
            // undo all pending operations
            TTransaction::rollback();
        }
    }
    
    /**
     * method onSave()
     * Executed whenever the user clicks at the save button
     */
    function onSave()
    {
        try
        {
            // open a transaction with database 'samples'
            TTransaction::open('permission');
            
            // get the form data into an active record Category
            $category = $this->form->getData('Enderecos');           
           
            // stores the object
            $category->store();
            
            // close the transaction
            TTransaction::close();
            
            // shows the success message
            new TMessage('info', 'Endereço Incluído com sucesso');
            
            // reload the listing
            $this->onReload();
        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', '<b>Error</b> ' . $e->getMessage());
            
            // undo all pending operations
            TTransaction::rollback();
        }
    }
    
    /**
     * method onEdit()
     * Executed whenever the user clicks at the edit button
     */
    function onEdit($param)
    {
        try
        {
            if (isset($param['key']))
            {
                // get the parameter e exibe mensagem
                $key=$param['key'];
                
                // open a transaction with database 'samples'
                TTransaction::open('permission');
                
                // instantiates object Category
                $category = new Enderecos($key);
                
                // lança os data do category no form
                $this->form->setData($category);
                
                // close the transaction
                TTransaction::close();
                $this->onReload();
            }
            else
            {
                $this->form->clear();
            }
        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', '<b>Error</b> ' . $e->getMessage());
            
            // undo all pending operations
            TTransaction::rollback();
        }
    }
    
    /**
     * method onDelete()
     * executed whenever the user clicks at the delete button
     * Ask if the user really wants to delete the record
     */
    function onDelete($param)
    {
        // define the delete action
        $action = new TAction(array($this, 'Delete'));
        $action->setParameters($param); // pass the key parameter ahead
        
        // shows a dialog to the user
        new TQuestion('Você deseja excluir este endereço ?', $action);
    }
    
    /**
     * method Delete()
     * Delete a record
     */
    function Delete($param)
    {
        try
        {
            // get the parameter $key
            $key=$param['key'];
            
            // open a transaction with database 'samples'
            TTransaction::open('permission');
            
            // instantiates object Category
            $category = new Enderecos($key);
            
            // deletes the object from the database
            $category->delete();
            
            // close the transaction
            TTransaction::close();
            
            // reload the listing
            $this->onReload( $param );
            // shows the success message
            new TMessage('info', "Endereço excluído com sucesso !");
        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', '<b>Error</b> ' . $e->getMessage());
            // undo all pending operations
            TTransaction::rollback();
        }
    }
    
    /**
     * method show()
     * Shows the page e seu conteúdo
     */
    function show()
    {
        // check if the datagrid is already loaded
        if (!$this->loaded)
        {
            $this->onReload( func_get_arg(0) );
        }
        parent::show();
    }
}

?>