<?php

session_start();

require_once __DIR__ . '\vendor\autoload.php';
require_once __DIR__ . '\Application\Autoload\Loader.php';
Application\Autoload\Loader::init(__DIR__);

//require_once __DIR__ . '\Application\Config\Database.config.php';
require_once __DIR__ . '\Application\Web\Server.php';
require_once __DIR__ . '\Application\View\View.php';
//require_once __DIR__ . '\Application\Entity\Loader.php';

use Phroute\Phroute\RouteCollector;
use phpseclib\Crypt\RSA;

function getCurrentUri()
{
    $basepath = implode('/', array_slice(explode('/', $_SERVER['SCRIPT_NAME']), 0, -1)) . '/';
    $uri = substr($_SERVER['REQUEST_URI'], strlen($basepath));
    if (strstr($uri, '?')) {
        $uri = substr($uri, 0, strpos($uri, '?'));
    }

    $uri = '/' . trim($uri, '/');
    return $uri;
}
//$table = new DbTable('customer');
//$authenticate = new Authenticate($table);
$router = new RouteCollector();

/*require_once __DIR__ . '\routes\web.php';

$router->filter('checkUser', function () {
if (isset($_SESSION['auth'])) {
header("Location: ./");
die();
}
});

$router->post('/login', function () {
$responseAuth = Authenticate::getInstance()->login();
if ($responseAuth->getStatus() == 302) {
header("Location: ./");
die();
} else {
header("Location: ./login");
die();
}
});

$router->get('/login', function () {
return View::render('login.html', ['csrf' => Authenticate::getInstance()->getToken()]);
}, ['before' => 'checkUser']);

$router->get('/logout', function () {
if (isset($_SESSION['auth'])) {
$_SESSION['auth'] = null;
}
header("Location: ./login");
die();
});
 */
$privateKey = '-----BEGIN RSA PRIVATE KEY-----
MIIEowIBAAKCAQEApKEbmgIIRYxdKznmalIUsZgNTE3O5rEyCDS4w5W1ZrRTMmCm
5WC683IPjQsZBifTclYHJ2+/YQxTwEqx9oD1CL2Ua3udpEO+rbPf+4hLpg193l1d
h6V9AZVlVoh09cfEp3klqdvNA+Ax7Z7ERLh+pAZ7Wfg9mQ8iSrTRvZQat8fzvepr
yoM5zYUzHBnZoURpVS38LgWT473TRY/5+zD0zrqsFzbulVSuul4+ZJ6aC6qJHdgR
J1bzhXuewIkEklUn2uuUIfCjTrXP3M9HRR0S3yI8l9tGwea8ev+1oxTUHI5Z5thH
8X3KlNB++QO5xp996wi2xyXHDV4AEXa+7vQ6hQIDAQABAoIBABK6HOis+IHo0KRd
Jr/LbH0zPbgDVdjaKUXTsXzWJ9dyBdDCO9n14e5VeD1kGMmheCUWciPjS1Lf0xsZ
pBrVWopO/nYnjqwj5Knfv0fenjRabon/9ua6RXkLpIZuaVeYDN+8ITOsRCr/ss5D
9DGZBxbjgd6fJWqm1RDIdt9o3I0xK+w4PfxU2g/JXqQW7GVF1K1Mu4NxFBDSKd9i
/fN6MEifnae35NpoNnRfCQASa6RKTYKrqxEynePL64TmMTtDXwl4/aOW8eb7Kpsr
9U+mUQiDS2dNqRH7LuVLLRCIb74IZdGKH2x1oYL6cu+/sAayLL7p43i02kzx5Rug
BU8TIgECgYEAz+IlCc9DWDsqKJK/6ln8V0jGodR1zQmnUV3lmJaW7MrXAHAiJsJ5
9HQJboAz6Vx8Ww2644eCP7I+fuggSW4pqmYncl/gyO6Jkk6fJz05a9EDvSIeiDO5
4uNAK8xr0CAKGM5yb7buYP0WZY4+Ih0fBOSn1AwKYlDwiIfR1z+KB4ECgYEAyrwA
XufRrC+32IXLgz1z46bHiz4901raSWXVQjsmNnuZjnywAlyn8cgMcj6/yv+JUs6W
nHn3JE6GDOVvArkl0FKHYbZd0iMDFOubJPRuf11c9s2TP+PGSFIjzA4ITEEwx8bG
cUstt8v+Ka/o73RoAgk088+73HM1mFBclqzklQUCgYEAo/BAq0rDXjpSVerc5FCQ
mrjuxEKLn+XECHvXC41+ekDAaz0DAbQOfwRfR8Bcr+TawOfEAZkk01yawnQGukHh
I9spsp3/5BWRcksEYep7dRZBL49PqrO4HUB/o8qzH0+VBtkQEB+gP+Z3GiGhyD9U
7gPwgl34dm5EMjeB+ZDHJoECgYAWyUapZBjW544cUf1rxM5vueEXr2k3hjCeq0lq
5kcHPKEnuK/3s/5UWI29kXvxDwOaQQoAkFzMwd6jOG5fufucDIqW2u50nebMxSZs
4uRAgHfgbX6tYaZ5nnHTj4gzOeiHq7nGJhL/Y3gvq0vdDhJamDpRZPnSb0iI1A90
B/6xGQKBgHgRNc/YYGp9FuKFJ8HodPRIW+WTzmh26DlTDPzRcQpMTJhef5cBKI27
Z/knvsjyYE9FcdEvXZUaRu0fdBknGCIeUx37pp5KHJKR6YdKcpiTAGajg7BiwvoS
cyPWMJ7PGWcPYXj3wDOZoLVsRogiCn30cy9pRVSUwMZKBpAXGvWI
-----END RSA PRIVATE KEY-----';
$router->put('/rawData', function () {
    global $privateKey;
    $_SESSION['putrequest'] = Server::getRequest()->getData();
    Server::getResponse()->setStatus(200);
    try {
        $rsa = new RSA();
        $rsa->loadKey($privateKey);
        return $rsa->decrypt(Server::getRequest()->getRawData());
    } catch (Throwable $e) {
        echo "failed " . $e->getMessage();
    }
});

$router->get('/session', function () {
    var_dump($_SESSION);
});

$router->get('/keys', function () {
    $publicKey='-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEApKEbmgIIRYxdKznmalIU
sZgNTE3O5rEyCDS4w5W1ZrRTMmCm5WC683IPjQsZBifTclYHJ2+/YQxTwEqx9oD1
CL2Ua3udpEO+rbPf+4hLpg193l1dh6V9AZVlVoh09cfEp3klqdvNA+Ax7Z7ERLh+
pAZ7Wfg9mQ8iSrTRvZQat8fzvepryoM5zYUzHBnZoURpVS38LgWT473TRY/5+zD0
zrqsFzbulVSuul4+ZJ6aC6qJHdgRJ1bzhXuewIkEklUn2uuUIfCjTrXP3M9HRR0S
3yI8l9tGwea8ev+1oxTUHI5Z5thH8X3KlNB++QO5xp996wi2xyXHDV4AEXa+7vQ6
hQIDAQAB
-----END PUBLIC KEY-----';
    global $privateKey;
    $rsa = new RSA();
    $rsa->setPublicKeyFormat(RSA::PUBLIC_FORMAT_XML);
    //$keys = $rsa->createKey(2048);
    $rsa->loadKey($privateKey);
    //$rsa->setPublicKey();
    //$privatekeyD = $rsa->getPrivateKey(); // could do RSA::PRIVATE_FORMAT_PKCS1 too
    //$publickeyD = $rsa->getPublicKey(RSA::PUBLIC_FORMAT_XML);

    file_put_contents('key.pri', $rsa->getPrivateKey());
    file_put_contents('key.pub', $rsa->getPublicKey(RSA::PUBLIC_FORMAT_XML));
    //echo $privatekeyD .'<br>';
    //echo $publickeyD;
});

$dispatcher = new Phroute\Phroute\Dispatcher($router->getData());

try {

    $response = $dispatcher->dispatch($_SERVER['REQUEST_METHOD'], getCurrentUri());

} catch (Phroute\Phroute\Exception\HttpRouteNotFoundException $e) {

//var_dump($e);
    echo View::render('errors/error.html', array('code' => 404, 'description' => $e->getMessage()));
    die();

} catch (Phroute\Phroute\Exception\HttpMethodNotAllowedException $e) {

//var_dump($e);
    echo View::render('errors/error.html', array('code' => 405, 'description' => $e->getMessage()));
    die();

}
Server::processResponse();
echo $response;

/*$rsa = new RSA();
//$keys = $rsa->createKey(2048);

//file_put_contents('key.pri', $keys['privatekey']);
//file_put_contents('key.pub', $keys['publickey']);
$publicKey='-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEApKEbmgIIRYxdKznmalIU
sZgNTE3O5rEyCDS4w5W1ZrRTMmCm5WC683IPjQsZBifTclYHJ2+/YQxTwEqx9oD1
CL2Ua3udpEO+rbPf+4hLpg193l1dh6V9AZVlVoh09cfEp3klqdvNA+Ax7Z7ERLh+
pAZ7Wfg9mQ8iSrTRvZQat8fzvepryoM5zYUzHBnZoURpVS38LgWT473TRY/5+zD0
zrqsFzbulVSuul4+ZJ6aC6qJHdgRJ1bzhXuewIkEklUn2uuUIfCjTrXP3M9HRR0S
3yI8l9tGwea8ev+1oxTUHI5Z5thH8X3KlNB++QO5xp996wi2xyXHDV4AEXa+7vQ6
hQIDAQAB
-----END PUBLIC KEY-----';

$rsa->loadKey($publicKey);
$ciphertext = $rsa->encrypt("Mi mama me mima");
echo '<pre>';
echo ($ciphertext);
echo '</pre>';
file_put_contents('result.txt', $ciphertext);

$rsa->loadKey($privateKey);

echo $rsa->decrypt(file_get_contents ('result.txt'));
echo $rsa->decrypt(file_get_contents ('csharp.txt'));*/
