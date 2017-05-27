Refactor Request and pagecontrollers to use Dependency Injection
Refactor getController out of frontcontroller, make it available to all controllers (forward())
Use Data Transer Objects instead of VOs, so setup is:
User <UserDTO> UserDAO <-> Database
User <UserDTO> ViewModel

ACL as filter
myacl->
    addrole: guest, member, admin
    addresource: user, blog, comment
    deny guest all, ->allow guest blog view, ->allow guest user view;
    allow member blog update id;
Auth for session handling
View->html helpers, like htmlentities etc;

RewriteEngine on
RewriteBase /ez-ebbs

RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d

RewriteRule ^(.*) index.php/?$1 [QSA,L]


TODO: