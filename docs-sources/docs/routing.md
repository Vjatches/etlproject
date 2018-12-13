# URI Routing

Application uses built-in CodeIgniter routing mechanism which allows to define or alter rules which regulate interaction with
the application from browser URI link.

Normal convention is:

`http://example.com/[controller-class]/[controller-method]/[arguments]`

CodeIgniter reads its routing rules from top to bottom and routes the request to the first matching rule. Each rule is a regular expression (left-side) mapped to a controller and method name separated by slashes (right-side). When a request comes in, CodeIgniter looks for the first match, and calls the appropriate controller and method, possibly with arguments.

# Routing rules used

Following rules have been used in the application:

    $route['default_controller'] = 'etl/home';
    
Default page shown, when nothing is provided is the page which is served by `home()` method of `Etl`
controller.
    
    $route['(:any)']='etl/$1';
    
Any page-request after base-url should be directed to `Etl` controller and appropriate method is tried to be found.
    
    $route['mongo/(:any)'] = 'etl/mongo/$1';
    
Any page request after base_url/mongo should be treated as accessing `mongo()` method of `Etl` controller with an argument specified as `$1` in page request.
    
    $route['sql/(:any)'] = 'etl/sql/$1';

Any page request after base_url/sql should be treated as accessing `sql()` method of `Etl` controller with an argument specified as `$1` in page request.

# Clean URI Generation

By default, the index.php file is included in URLs:

`example.com/index.php/etl/mongo/extracted`

If your Apache server has `mod_rewrite` enabled, you can easily remove this file by using a .htaccess file with some simple rules. 
Here is an example of such a file, using the “negative” method in which everything is redirected except the specified items:

    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ index.php/$1 [L]
    
n the above example, any HTTP request other than those for existing directories and existing files is treated as a request for your index.php file.

**Note!**  These specific rules might not work for all server configurations.