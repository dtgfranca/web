<?php
class Estabelecimento extends TPage
{
    /**
     * Constructor method
     */
    public function __construct()
    {
        parent::__construct();
        
        // load the styles
        TPage::include_css('app/resources/catalog.css');
        
        // create the HTML Renderer
        $this->html = new THtmlRenderer('app/resources/catalog.html');

        // define replacements for the main section
        $replace = array();
        
        // replace the main section variables
        $this->html->enableSection('main', $replace);
        
        $this->enableManagement();
        
        try
        {
            // load the products
            TTransaction::open('samples');
            $criteria = new TCriteria;
            $criteria->add(new TFilter('photo_path', '<>', ''));
            $products = Product::getObjects($criteria);
            TTransaction::close();
            
            $replace_detail = array();
            if ($products)
            {
                // iterate products
                foreach ($products as $product)
                {
                    $replace_detail[] = $product->toArray(); // array of replacements
                }
            }
            
            // enable products section as repeatable
            $this->html->enableSection('products', $replace_detail, TRUE);
            
            // wrap the page content using vertical box
            $vbox = new TVBox;
            $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
            $vbox->add($this->html);
    
            parent::add($vbox);            
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
    
    /**
     * Enable or not the 'manage' section
     */
    public function enableManagement()
    {
        if (is_array(TSession::getValue('cart_items')) AND count(TSession::getValue('cart_items')) > 0)
        {
            $this->html->enableSection('manage');
        }
    }
    
    /**
     * Executed when the user clicks at click to buy button
     */
    public function onBuyClick( $param )
    {
        $cart_items = TSession::getValue('cart_items');
        if (isset($cart_items[ $param['product_id'] ]))
        {
            $cart_items[ $param['product_id'] ] ++;
        }
        else
        {
            $cart_items[ $param['product_id'] ] = 1;
        }
        
        TSession::setValue('cart_items', $cart_items);
        
        $this->enableManagement();
        
        $posAction = new TAction( array('CartManagementView', 'onReload') );
        new TMessage('info', 'You have chosen the product: ' . $param['product_id'], $posAction);
    }
}

?>