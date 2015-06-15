# currency_converter
This PHP class converts currencies. Not all currencies are supported, but major ones are.

# Installation
using composer:

    composer require dleger/currency_converter:dev-master

# How to use it
    <?php
    require_once('vendor/autoload.php');
    
    use dleger\currency_converter\currency_converter;
    
    $conversion = new currency_converter(FALSE, '', '');
    
    echo $conversion->convert('GBP', 'EUR', 100);
    ?>

You can also have a cache functionnality:

replace:

    <?php
    $conversion = new currency_converter(FALSE, '', '');
    ?>

by:

    <?php
    $conversion = new currency_converter(TRUE, 'your/cache/folder', 3600); // 3600 is the expiry time
    ?>

